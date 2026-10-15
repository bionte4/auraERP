<?php

use Livewire\Livewire;
use Webkul\Chatter\Services\AuraAiAssistantService;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Partner\Models\Partner;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Models\PosCashMovement;
use Webkul\Pos\Models\PosOrder;
use Webkul\Pos\Models\PosPaymentMethod;
use Webkul\Pos\Models\PosRegister;
use Webkul\Pos\Models\PosSession;
use Webkul\Pos\Services\PosService;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

beforeEach(function (): void {
    $migration = include __DIR__.'/../../database/migrations/2026_10_15_100000_create_pos_tables.php';
    $migration->up();

    PosOrder::query()->delete();
    PosCashMovement::query()->delete();
    PosSession::query()->delete();
    PosRegister::query()->delete();
    PosPaymentMethod::query()->delete();
});

it('seeds default POS payment methods and register automatically', function (): void {
    $posService = app(PosService::class);
    $posService->ensureDefaultSetup();

    expect(PosPaymentMethod::query()->count())->toBe(4)
        ->and(PosRegister::query()->count())->toBe(1);

    $register = PosRegister::query()->first();
    expect($register->code)->toBe('REG-01')
        ->and($register->receipt_paper_size)->toBe('80mm');
});

it('manages cashier shift session, cash movements, multi-payment checkout, automatic stock deduction, refund, and WhatsApp receipt', function (): void {
    $cashier = User::query()->firstOrCreate(
        ['email' => 'kasir.pos@example.com'],
        ['name' => 'Budi Kasir', 'password' => bcrypt('secret')]
    );

    $this->actingAs($cashier);

    $location = Location::factory()->create([
        'name'      => 'Gudang Toko Utama',
        'full_name' => 'WH/Stock Toko',
    ]);

    $productA = Product::factory()->create([
        'name'         => 'Kopi Susu Botol 250ml',
        'reference'    => 'SKU-KOPI-01',
        'barcode'      => '8991002003001',
        'price'        => 25000,
        'cost'         => 15000,
        'enable_sales' => true,
    ]);

    $productB = Product::factory()->create([
        'name'         => 'Roti Gandum Lapis',
        'reference'    => 'SKU-ROTI-02',
        'barcode'      => '8991002003002',
        'price'        => 15000,
        'cost'         => 9000,
        'enable_sales' => true,
    ]);

    ProductQuantity::query()->withoutGlobalScopes()->create([
        'product_id'        => $productA->id,
        'location_id'       => $location->id,
        'quantity'          => 50,
        'reserved_quantity' => 0,
        'company_id'        => $productA->company_id,
        'creator_id'        => $cashier->id,
    ]);

    ProductQuantity::query()->withoutGlobalScopes()->create([
        'product_id'        => $productB->id,
        'location_id'       => $location->id,
        'quantity'          => 20,
        'reserved_quantity' => 0,
        'company_id'        => $productB->company_id,
        'creator_id'        => $cashier->id,
    ]);

    $posService = app(PosService::class);
    $posService->ensureDefaultSetup();

    $register = PosRegister::query()->firstOrFail();
    $register->update([
        'stock_location_id' => $location->id,
        'default_tax_rate'  => 10,
        'auto_deduct_stock' => true,
    ]);

    $customer = Partner::factory()->create([
        'name'         => 'Ibu Sari Retail',
        'mobile'       => '081298765432',
        'sub_type'     => 'customer',
        'account_type' => 'individual',
    ]);

    // 1. Open Cashier Session with Rp 200.000 opening cash
    $session = $posService->openSession($register, 200000, $cashier->id, 'Shift Pagi');

    expect($session->status)->toBe(PosSession::STATUS_OPEN)
        ->and((float) $session->opening_balance)->toBe(200000.0)
        ->and((float) $session->expected_closing_cash)->toBe(200000.0);

    // 2. Record Cash In (Rp 50.000) and Cash Out (Rp 15.000)
    $posService->recordCashMovement($session, PosCashMovement::TYPE_IN, 50000, 'Tambah uang receh', $cashier->id);
    $posService->recordCashMovement($session, PosCashMovement::TYPE_OUT, 15000, 'Beli kantong belanja', $cashier->id);

    $session->refresh();
    expect((float) $session->cash_in_total)->toBe(50000.0)
        ->and((float) $session->cash_out_total)->toBe(15000.0)
        ->and((float) $session->expected_closing_cash)->toBe(235000.0);

    // 3. Checkout with Multi-Payment (Split Bill: Cash + QRIS)
    $cashMethod = PosPaymentMethod::query()->where('code', 'CASH')->firstOrFail();
    $qrisMethod = PosPaymentMethod::query()->where('code', 'QRIS')->firstOrFail();

    $order = $posService->checkout(
        session: $session,
        items: [
            [
                'product_id'      => $productA->id,
                'product_name'    => $productA->name,
                'barcode'         => $productA->barcode,
                'sku'             => $productA->reference,
                'qty'             => 2,
                'unit_price'      => 25000,
                'discount_amount' => 0,
            ],
            [
                'product_id'      => $productB->id,
                'product_name'    => $productB->name,
                'barcode'         => $productB->barcode,
                'sku'             => $productB->reference,
                'qty'             => 2,
                'unit_price'      => 15000,
                'discount_amount' => 5000,
            ],
        ],
        payments: [
            [
                'payment_method_id' => $qrisMethod->id,
                'amount'            => 50000,
                'reference'         => 'QRIS-REF-991',
            ],
            [
                'payment_method_id' => $cashMethod->id,
                'amount'            => 30000,
                'reference'         => '',
            ],
        ],
        partnerId: $customer->id,
        orderDiscountAmount: 5000,
        taxRate: 10,
        note: 'Transaksi Split Bill',
        status: PosOrder::STATUS_PAID,
        cashierId: $cashier->id,
    );

    expect($order->status)->toBe(PosOrder::STATUS_PAID)
        ->and((float) $order->subtotal)->toBe(75000.0)
        ->and((float) $order->discount_amount)->toBe(5000.0)
        ->and((float) $order->tax_amount)->toBe(7000.0)
        ->and((float) $order->total_amount)->toBe(77000.0)
        ->and((float) $order->paid_amount)->toBe(80000.0)
        ->and((float) $order->change_amount)->toBe(3000.0)
        ->and($order->stock_deducted)->toBeTrue();

    // 4. Verify Warehouse Stock Deduction (50 -> 48, 20 -> 18)
    expect($posService->getAvailableStock($productA->id, $register))->toBe(48.0)
        ->and($posService->getAvailableStock($productB->id, $register))->toBe(18.0);

    // 5. Verify Session Totals after Sale
    // Expected Cash = 200.000 (open) + 50.000 (in) - 15.000 (out) + 27.000 (net cash sale) = 262.000
    $session->refresh();
    expect((float) $session->cash_sales_total)->toBe(27000.0)
        ->and((float) $session->non_cash_sales_total)->toBe(50000.0)
        ->and((float) $session->expected_closing_cash)->toBe(262000.0);

    // 6. Verify WhatsApp Receipt URL & Text
    $waUrl = $posService->buildWhatsappReceiptUrl($order);
    $waText = $posService->buildWhatsappReceiptText($order);

    expect($waUrl)->toStartWith('https://wa.me/6281298765432?text=')
        ->and($waText)->toContain($order->name)
        ->and($waText)->toContain('Kopi Susu Botol 250ml')
        ->and($waText)->toContain('Rp 77.000');

    // 7. Verify auraAI Assistant POS Insights
    $cashier->assignRole('Admin');
    $aiText = app(AuraAiAssistantService::class)->answer('Bagaimana ringkasan omzet kasir POS hari ini?');
    expect($aiText)->toContain('Ringkasan Kasir Retail (POS)')
        ->and($aiText)->toContain('77.000');

    // 8. Close Session (Z-Report) with actual cash Rp 262.000
    $closedSession = $posService->closeSession($session, 262000, 'Klop tidak ada selisih');
    expect($closedSession->status)->toBe(PosSession::STATUS_CLOSED)
        ->and((float) $closedSession->cash_difference)->toBe(0.0);

    // 9. Refund Order & Verify Stock Restoration (48 -> 50, 18 -> 20)
    $refunded = $posService->refundOrder($order, 'Pelanggan tukar barang');
    expect($refunded->status)->toBe(PosOrder::STATUS_REFUNDED)
        ->and($posService->getAvailableStock($productA->id, $register))->toBe(50.0)
        ->and($posService->getAvailableStock($productB->id, $register))->toBe(20.0);
});

it('supports barcode scanning, hold bill recall, and checkout via PosTerminalPage Livewire component', function (): void {
    $cashier = User::query()->firstOrCreate(
        ['email' => 'kasir.livewire@example.com'],
        ['name' => 'Siti Kasir', 'password' => bcrypt('secret')]
    );
    $cashier->assignRole('Kasir');

    $this->actingAs($cashier);

    $product = Product::factory()->create([
        'name'         => 'Teh Hijau Botol 500ml',
        'reference'    => 'SKU-TEH-99',
        'barcode'      => '8997778889991',
        'price'        => 12000,
        'enable_sales' => true,
    ]);

    Livewire::test(PosTerminalPage::class)
        ->set('openingBalanceInput', 100000)
        ->call('openSession')
        ->set('barcodeInput', '8997778889991')
        ->call('scanBarcode')
        ->assertSet('barcodeInput', '')
        ->call('parkCurrentOrder')
        ->assertSet('cart', []);

    $parked = PosOrder::query()->where('status', PosOrder::STATUS_PARKED)->first();
    expect($parked)->not->toBeNull();

    Livewire::test(PosTerminalPage::class)
        ->call('recallParkedOrder', $parked->id)
        ->call('setQuickCashAmount', 20000)
        ->call('processCheckout');

    $paidOrder = PosOrder::query()->where('status', PosOrder::STATUS_PAID)->latest('id')->first();
    expect($paidOrder)->not->toBeNull()
        ->and((float) $paidOrder->total_amount)->toBe(12000.0)
        ->and((float) $paidOrder->paid_amount)->toBe(20000.0)
        ->and((float) $paidOrder->change_amount)->toBe(8000.0);
});
