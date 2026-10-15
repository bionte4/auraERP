<?php

namespace Webkul\Pos\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;
use UnitEnum;
use Webkul\Partner\Models\Partner;
use Webkul\Pos\Models\PosCashMovement;
use Webkul\Pos\Models\PosOrder;
use Webkul\Pos\Models\PosPaymentMethod;
use Webkul\Pos\Models\PosRegister;
use Webkul\Pos\Models\PosSession;
use Webkul\Pos\Services\PosService;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Support\Enums\NavigationGroup;

class PosTerminalPage extends Page
{
    protected string $view = 'pos::filament.pages.pos-terminal';

    protected static ?string $slug = 'pos/terminal';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?int $navigationSort = 1;

    public ?int $selectedRegisterId = null;

    public ?int $activeSessionId = null;

    public string $barcodeInput = '';

    public string $searchQuery = '';

    public ?int $selectedCategoryId = null;

    public ?int $selectedPartnerId = null;

    /**
     * @var array<string, array{key: string, product_id: int|null, product_name: string, barcode: string|null, sku: string|null, qty: float, unit_price: float, discount_amount: float, available_stock: float}>
     */
    public array $cart = [];

    public float $orderDiscountAmount = 0;

    public float $taxRate = 0;

    public string $orderNote = '';

    /**
     * @var array<int, array{payment_method_id: int, amount: float, reference: string}>
     */
    public array $paymentLines = [];

    public float $openingBalanceInput = 0;

    public string $openingNotesInput = '';

    public bool $showCashMovementModal = false;

    public string $cashMovementType = PosCashMovement::TYPE_IN;

    public float $cashMovementAmount = 0;

    public string $cashMovementReason = '';

    public bool $showCloseSessionModal = false;

    public float $closingCashCountedInput = 0;

    public string $closingNotesInput = '';

    public bool $showQuickCustomerModal = false;

    public string $newCustomerName = '';

    public string $newCustomerPhone = '';

    public ?int $receiptOrderId = null;

    public string $whatsappPhoneInput = '';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Pos;
    }

    public static function getNavigationLabel(): string
    {
        return 'Layar Kasir (POS)';
    }

    public function getTitle(): string
    {
        return 'Terminal Kasir Retail (POS)';
    }

    public function mount(): void
    {
        app(PosService::class)->ensureDefaultSetup(
            auth()->user()?->default_company_id,
            auth()->id()
        );

        $requestedSessionId = request()->integer('session_id');
        $requestedRegisterId = request()->integer('register_id');

        if ($requestedSessionId) {
            $session = PosSession::query()->find($requestedSessionId);

            if ($session && $session->status === PosSession::STATUS_OPEN) {
                $this->activeSessionId = $session->id;
                $this->selectedRegisterId = $session->pos_register_id;
            }
        }

        if (! $this->selectedRegisterId) {
            if ($requestedRegisterId) {
                $this->selectedRegisterId = $requestedRegisterId;
            } else {
                $this->selectedRegisterId = PosRegister::query()
                    ->where('is_active', true)
                    ->value('id');
            }
        }

        $this->syncActiveSessionForRegister();
        $this->initializeDefaultPaymentLine();

        $receiptId = request()->integer('receipt_order_id');
        if ($receiptId) {
            $this->openReceipt($receiptId);
        }
    }

    public function updatedSelectedRegisterId(): void
    {
        $this->syncActiveSessionForRegister();
    }

    protected function syncActiveSessionForRegister(): void
    {
        if (! $this->selectedRegisterId) {
            $this->activeSessionId = null;

            return;
        }

        $register = PosRegister::query()->find($this->selectedRegisterId);

        if ($register) {
            $this->taxRate = (float) $register->default_tax_rate;
        }

        $openSession = PosSession::query()
            ->where('pos_register_id', $this->selectedRegisterId)
            ->where('status', PosSession::STATUS_OPEN)
            ->latest('id')
            ->first();

        $this->activeSessionId = $openSession?->id;
    }

    protected function initializeDefaultPaymentLine(): void
    {
        $defaultMethodId = PosPaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->value('id');

        if ($defaultMethodId && empty($this->paymentLines)) {
            $this->paymentLines = [
                [
                    'payment_method_id' => (int) $defaultMethodId,
                    'amount'            => 0,
                    'reference'         => '',
                ],
            ];
        }
    }

    public function openSession(): void
    {
        $register = PosRegister::query()->find($this->selectedRegisterId);

        if (! $register) {
            Notification::make()
                ->warning()
                ->title('Pilih Mesin Kasir terlebih dahulu.')
                ->send();

            return;
        }

        $session = app(PosService::class)->openSession(
            $register,
            (float) $this->openingBalanceInput,
            auth()->id(),
            $this->openingNotesInput ?: null
        );

        $this->activeSessionId = $session->id;
        $this->taxRate = (float) $register->default_tax_rate;
        $this->openingBalanceInput = 0;
        $this->openingNotesInput = '';

        Notification::make()
            ->success()
            ->title("Sesi Kasir {$session->name} berhasil dibuka.")
            ->send();
    }

    public function scanBarcode(): void
    {
        $code = trim($this->barcodeInput);

        if ($code === '') {
            return;
        }

        $product = Product::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($code): void {
                $query->where('barcode', $code)
                    ->orWhere('reference', $code);
            })
            ->first();

        if (! $product) {
            $product = Product::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('name', 'like', "%{$code}%")
                ->first();
        }

        if (! $product) {
            Notification::make()
                ->danger()
                ->title("Produk dengan Barcode/SKU '{$code}' tidak ditemukan.")
                ->send();

            return;
        }

        $this->addToCart($product->id);
        $this->barcodeInput = '';
    }

    public function addToCart(int $productId): void
    {
        $product = Product::query()
            ->withoutGlobalScopes()
            ->find($productId);

        if (! $product) {
            return;
        }

        $register = $this->getActiveRegister();
        $availableStock = app(PosService::class)->getAvailableStock($product->id, $register);
        $key = 'prod_'.$product->id;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty'] = round((float) $this->cart[$key]['qty'] + 1, 2);
            $this->cart[$key]['available_stock'] = $availableStock;
        } else {
            $this->cart[$key] = [
                'key'             => $key,
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'barcode'         => $product->barcode,
                'sku'             => $product->reference,
                'qty'             => 1,
                'unit_price'      => round((float) ($product->price ?? 0), 2),
                'discount_amount' => 0,
                'available_stock' => $availableStock,
            ];
        }

        $this->syncSinglePaymentAmountWithTotal();
    }

    public function incrementQty(string $key): void
    {
        if (! isset($this->cart[$key])) {
            return;
        }

        $this->cart[$key]['qty'] = round((float) $this->cart[$key]['qty'] + 1, 2);
        $this->syncSinglePaymentAmountWithTotal();
    }

    public function decrementQty(string $key): void
    {
        if (! isset($this->cart[$key])) {
            return;
        }

        $nextQty = round((float) $this->cart[$key]['qty'] - 1, 2);

        if ($nextQty <= 0) {
            unset($this->cart[$key]);
        } else {
            $this->cart[$key]['qty'] = $nextQty;
        }

        $this->syncSinglePaymentAmountWithTotal();
    }

    public function removeCartItem(string $key): void
    {
        unset($this->cart[$key]);
        $this->syncSinglePaymentAmountWithTotal();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->orderDiscountAmount = 0;
        $this->orderNote = '';
        $this->initializeDefaultPaymentLine();
        $this->syncSinglePaymentAmountWithTotal();
    }

    public function updatedCart(): void
    {
        $this->syncSinglePaymentAmountWithTotal();
    }

    public function updatedOrderDiscountAmount(): void
    {
        $this->syncSinglePaymentAmountWithTotal();
    }

    public function updatedTaxRate(): void
    {
        $this->syncSinglePaymentAmountWithTotal();
    }

    protected function syncSinglePaymentAmountWithTotal(): void
    {
        if (count($this->paymentLines) === 1) {
            $this->paymentLines[0]['amount'] = $this->getCartTotal();
        }
    }

    public function setQuickCashAmount(float $amount): void
    {
        if (empty($this->paymentLines)) {
            $this->initializeDefaultPaymentLine();
        }

        $this->paymentLines[0]['amount'] = round($amount, 2);
    }

    public function setExactCashAmount(): void
    {
        $this->setQuickCashAmount($this->getCartTotal());
    }

    public function addPaymentLine(): void
    {
        $methodId = PosPaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->value('id');

        $remaining = max(0, round($this->getCartTotal() - $this->getTotalPaidInput(), 2));

        $this->paymentLines[] = [
            'payment_method_id' => (int) $methodId,
            'amount'            => $remaining,
            'reference'         => '',
        ];
    }

    public function removePaymentLine(int $index): void
    {
        if (count($this->paymentLines) <= 1) {
            return;
        }

        unset($this->paymentLines[$index]);
        $this->paymentLines = array_values($this->paymentLines);
    }

    public function getCartSubtotal(): float
    {
        $subtotal = 0.0;

        foreach ($this->cart as $item) {
            $gross = round(max(0.01, (float) ($item['qty'] ?? 1)) * max(0, (float) ($item['unit_price'] ?? 0)), 2);
            $disc = min($gross, round(max(0, (float) ($item['discount_amount'] ?? 0)), 2));
            $subtotal += round($gross - $disc, 2);
        }

        return round($subtotal, 2);
    }

    public function getTaxAmount(): float
    {
        $subtotal = $this->getCartSubtotal();
        $discount = min($subtotal, round(max(0, (float) $this->orderDiscountAmount), 2));
        $base = max(0, $subtotal - $discount);

        return round($base * (max(0, (float) $this->taxRate) / 100), 2);
    }

    public function getCartTotal(): float
    {
        $subtotal = $this->getCartSubtotal();
        $discount = min($subtotal, round(max(0, (float) $this->orderDiscountAmount), 2));

        return round(max(0, $subtotal - $discount) + $this->getTaxAmount(), 2);
    }

    public function getTotalPaidInput(): float
    {
        $paid = 0.0;

        foreach ($this->paymentLines as $line) {
            $paid += max(0, (float) ($line['amount'] ?? 0));
        }

        return round($paid, 2);
    }

    public function getChangeAmount(): float
    {
        return round(max(0, $this->getTotalPaidInput() - $this->getCartTotal()), 2);
    }

    public function processCheckout(): void
    {
        $session = $this->getActiveSession();

        if (! $session) {
            Notification::make()
                ->warning()
                ->title('Sesi kasir belum dibuka.')
                ->send();

            return;
        }

        try {
            $order = app(PosService::class)->checkout(
                session: $session,
                items: array_values($this->cart),
                payments: $this->paymentLines,
                partnerId: $this->selectedPartnerId,
                orderDiscountAmount: (float) $this->orderDiscountAmount,
                taxRate: (float) $this->taxRate,
                note: $this->orderNote ?: null,
                status: PosOrder::STATUS_PAID,
                cashierId: auth()->id(),
            );

            $this->clearCart();
            $this->openReceipt($order->id);

            Notification::make()
                ->success()
                ->title("Transaksi {$order->name} berhasil disimpan!")
                ->body('Total: Rp '.number_format((float) $order->total_amount, 0, ',', '.').' | Kembalian: Rp '.number_format((float) $order->change_amount, 0, ',', '.'))
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal memproses pembayaran')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function parkCurrentOrder(): void
    {
        $session = $this->getActiveSession();

        if (! $session || empty($this->cart)) {
            return;
        }

        try {
            $order = app(PosService::class)->checkout(
                session: $session,
                items: array_values($this->cart),
                payments: [],
                partnerId: $this->selectedPartnerId,
                orderDiscountAmount: (float) $this->orderDiscountAmount,
                taxRate: (float) $this->taxRate,
                note: $this->orderNote ?: 'Pesanan Ditahan (Hold)',
                status: PosOrder::STATUS_PARKED,
                cashierId: auth()->id(),
            );

            $this->clearCart();

            Notification::make()
                ->info()
                ->title("Transaksi {$order->name} berhasil ditahan (Hold Bill).")
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal menahan pesanan')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function recallParkedOrder(int $orderId): void
    {
        $order = PosOrder::query()->with('lines')->find($orderId);

        if (! $order || $order->status !== PosOrder::STATUS_PARKED) {
            return;
        }

        $register = $this->getActiveRegister();
        $posService = app(PosService::class);

        $this->cart = [];
        foreach ($order->lines as $line) {
            $key = $line->product_id ? 'prod_'.$line->product_id : 'line_'.$line->id;
            $this->cart[$key] = [
                'key'             => $key,
                'product_id'      => $line->product_id,
                'product_name'    => $line->product_name,
                'barcode'         => $line->barcode,
                'sku'             => $line->sku,
                'qty'             => (float) $line->qty,
                'unit_price'      => (float) $line->unit_price,
                'discount_amount' => (float) $line->discount_amount,
                'available_stock' => $line->product_id ? $posService->getAvailableStock($line->product_id, $register) : 0,
            ];
        }

        $this->selectedPartnerId = $order->partner_id;
        $this->orderDiscountAmount = (float) $order->discount_amount;
        $this->taxRate = (float) $order->tax_rate;
        $this->orderNote = (string) ($order->note ?? '');

        $order->delete();
        $this->syncSinglePaymentAmountWithTotal();

        Notification::make()
            ->success()
            ->title("Pesanan {$order->name} dimuat kembali ke keranjang.")
            ->send();
    }

    public function submitCashMovement(): void
    {
        $session = $this->getActiveSession();

        if (! $session) {
            return;
        }

        try {
            app(PosService::class)->recordCashMovement(
                $session,
                $this->cashMovementType,
                (float) $this->cashMovementAmount,
                $this->cashMovementReason,
                auth()->id()
            );

            $this->cashMovementAmount = 0;
            $this->cashMovementReason = '';
            $this->showCashMovementModal = false;

            Notification::make()
                ->success()
                ->title('Mutasi kas berhasil dicatat.')
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal mencatat mutasi kas')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function prepareCloseSessionModal(): void
    {
        $session = $this->getActiveSession();

        if (! $session) {
            return;
        }

        $session = app(PosService::class)->recalculateSessionTotals($session);
        $this->closingCashCountedInput = (float) $session->expected_closing_cash;
        $this->closingNotesInput = '';
        $this->showCloseSessionModal = true;
    }

    public function confirmCloseSession(): void
    {
        $session = $this->getActiveSession();

        if (! $session) {
            return;
        }

        $closedSession = app(PosService::class)->closeSession(
            $session,
            (float) $this->closingCashCountedInput,
            $this->closingNotesInput ?: null
        );

        $this->showCloseSessionModal = false;
        $this->activeSessionId = null;
        $this->clearCart();

        Notification::make()
            ->success()
            ->title("Sesi {$closedSession->name} berhasil ditutup (Z-Report tersimpan).")
            ->body('Selisih Kas: Rp '.number_format((float) $closedSession->cash_difference, 0, ',', '.'))
            ->send();
    }

    public function createQuickCustomer(): void
    {
        $name = trim($this->newCustomerName);

        if ($name === '') {
            Notification::make()
                ->warning()
                ->title('Nama pelanggan wajib diisi.')
                ->send();

            return;
        }

        $partner = Partner::query()->create([
            'name'         => $name,
            'phone'        => trim($this->newCustomerPhone) ?: null,
            'mobile'       => trim($this->newCustomerPhone) ?: null,
            'sub_type'     => 'customer',
            'account_type' => 'individual',
            'company_id'   => auth()->user()?->default_company_id,
            'creator_id'   => auth()->id(),
        ]);

        $this->selectedPartnerId = $partner->id;
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->showQuickCustomerModal = false;

        Notification::make()
            ->success()
            ->title("Pelanggan '{$partner->name}' berhasil ditambahkan.")
            ->send();
    }

    public function openReceipt(int $orderId): void
    {
        $order = PosOrder::query()->with('partner')->find($orderId);

        if (! $order) {
            return;
        }

        $this->receiptOrderId = $order->id;
        $this->whatsappPhoneInput = (string) ($order->partner?->mobile ?: $order->partner?->phone ?: '');
    }

    public function closeReceipt(): void
    {
        $this->receiptOrderId = null;
    }

    public function getActiveRegister(): ?PosRegister
    {
        return $this->selectedRegisterId
            ? PosRegister::query()->find($this->selectedRegisterId)
            : null;
    }

    public function getActiveSession(): ?PosSession
    {
        return $this->activeSessionId
            ? PosSession::query()->with(['register', 'user'])->find($this->activeSessionId)
            : null;
    }

    public function getReceiptOrder(): ?PosOrder
    {
        return $this->receiptOrderId
            ? PosOrder::query()->with(['lines', 'payments', 'register', 'partner', 'cashier', 'session'])->find($this->receiptOrderId)
            : null;
    }

    public function getWhatsappUrl(): ?string
    {
        $order = $this->getReceiptOrder();

        if (! $order) {
            return null;
        }

        return app(PosService::class)->buildWhatsappReceiptUrl($order, $this->whatsappPhoneInput ?: null);
    }

    public function getRegisters(): Collection
    {
        return PosRegister::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getCategories(): Collection
    {
        if (! Schema::hasTable('products_categories')) {
            return collect();
        }

        return Category::query()
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function getProducts(): Collection
    {
        if (! Schema::hasTable('products_products')) {
            return collect();
        }

        $query = Product::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where(function ($q): void {
                $q->where('enable_sales', true)
                    ->orWhereNull('enable_sales');
            });

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        $search = trim($this->searchQuery);
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->limit(36)->get();
        $register = $this->getActiveRegister();
        $posService = app(PosService::class);

        return $products->map(function (Product $product) use ($posService, $register): array {
            return [
                'id'              => $product->id,
                'name'            => $product->name,
                'sku'             => $product->reference,
                'barcode'         => $product->barcode,
                'price'           => (float) ($product->price ?? 0),
                'available_stock' => $posService->getAvailableStock($product->id, $register),
            ];
        });
    }

    public function getCustomers(): Collection
    {
        if (! Schema::hasTable('partners_partners')) {
            return collect();
        }

        return Partner::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'phone', 'mobile']);
    }

    public function getPaymentMethods(): Collection
    {
        return PosPaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();
    }

    public function getParkedOrders(): Collection
    {
        if (! $this->activeSessionId) {
            return collect();
        }

        return PosOrder::query()
            ->where('pos_session_id', $this->activeSessionId)
            ->where('status', PosOrder::STATUS_PARKED)
            ->latest('id')
            ->get();
    }

    public function getRecentSessionOrders(): Collection
    {
        if (! $this->activeSessionId) {
            return collect();
        }

        return PosOrder::query()
            ->where('pos_session_id', $this->activeSessionId)
            ->where('status', PosOrder::STATUS_PAID)
            ->latest('id')
            ->limit(8)
            ->get();
    }
}
