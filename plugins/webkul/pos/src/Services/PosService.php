<?php

namespace Webkul\Pos\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Pos\Models\PosCashMovement;
use Webkul\Pos\Models\PosOrder;
use Webkul\Pos\Models\PosOrderPayment;
use Webkul\Pos\Models\PosPaymentMethod;
use Webkul\Pos\Models\PosRegister;
use Webkul\Pos\Models\PosSession;
use Webkul\Product\Models\Product;

class PosService
{
    public function ensureDefaultSetup(?int $companyId = null, ?int $userId = null): void
    {
        if (! Schema::hasTable('pos_registers') || ! Schema::hasTable('pos_payment_methods')) {
            return;
        }

        if (PosPaymentMethod::query()->count() === 0) {
            $methods = [
                [
                    'name'           => 'Tunai (Cash)',
                    'code'           => 'CASH',
                    'type'           => PosPaymentMethod::TYPE_CASH,
                    'account_number' => null,
                    'sort'           => 1,
                    'is_active'      => true,
                    'company_id'     => $companyId,
                ],
                [
                    'name'           => 'QRIS / E-Wallet',
                    'code'           => 'QRIS',
                    'type'           => PosPaymentMethod::TYPE_QRIS,
                    'account_number' => 'QRIS Merchant',
                    'sort'           => 2,
                    'is_active'      => true,
                    'company_id'     => $companyId,
                ],
                [
                    'name'           => 'Transfer Bank',
                    'code'           => 'TRANSFER',
                    'type'           => PosPaymentMethod::TYPE_TRANSFER,
                    'account_number' => 'BCA / Mandiri',
                    'sort'           => 3,
                    'is_active'      => true,
                    'company_id'     => $companyId,
                ],
                [
                    'name'           => 'Kartu Debit / Kredit (EDC)',
                    'code'           => 'CARD',
                    'type'           => PosPaymentMethod::TYPE_CARD,
                    'account_number' => 'Mesin EDC',
                    'sort'           => 4,
                    'is_active'      => true,
                    'company_id'     => $companyId,
                ],
            ];

            foreach ($methods as $method) {
                PosPaymentMethod::query()->firstOrCreate(
                    ['code' => $method['code']],
                    $method
                );
            }
        }

        if (PosRegister::query()->count() === 0) {
            $warehouse = Schema::hasTable('inventories_warehouses')
                ? Warehouse::withTrashed()->withoutGlobalScopes()->first()
                : null;

            $locationId = $warehouse?->lot_stock_location_id;

            if (! $locationId && Schema::hasTable('inventories_locations')) {
                $locationId = Location::withTrashed()
                    ->withoutGlobalScopes()
                    ->where('type', 'internal')
                    ->value('id');
            }

            PosRegister::query()->create([
                'name'                    => 'Kasir Utama #1',
                'code'                    => 'REG-01',
                'warehouse_id'            => $warehouse?->id,
                'stock_location_id'       => $locationId,
                'default_tax_rate'        => 0,
                'auto_deduct_stock'       => true,
                'allow_out_of_stock_sale' => true,
                'receipt_paper_size'      => '80mm',
                'receipt_header'          => "auraERP Retail Store\nJl. Utama No. 1, Indonesia",
                'receipt_footer'          => 'Terima kasih atas kunjungan Anda! Barang yang sudah dibeli tidak dapat ditukar kecuali ada perjanjian.',
                'is_active'               => true,
                'company_id'              => $companyId ?? $warehouse?->company_id,
                'creator_id'              => $userId,
            ]);
        }
    }

    public function openSession(
        PosRegister $register,
        float $openingBalance = 0,
        ?int $userId = null,
        ?string $notes = null
    ): PosSession {
        $existingOpen = PosSession::query()
            ->where('pos_register_id', $register->id)
            ->where('status', PosSession::STATUS_OPEN)
            ->first();

        if ($existingOpen) {
            return $existingOpen;
        }

        $nextNumber = PosSession::query()->count() + 1;
        $sessionName = sprintf('POS/%s/%04d', now()->format('Y/m'), $nextNumber);

        while (PosSession::query()->where('name', $sessionName)->exists()) {
            $nextNumber++;
            $sessionName = sprintf('POS/%s/%04d', now()->format('Y/m'), $nextNumber);
        }

        return PosSession::query()->create([
            'name'                  => $sessionName,
            'pos_register_id'       => $register->id,
            'user_id'               => $userId ?? auth()->id(),
            'status'                => PosSession::STATUS_OPEN,
            'opened_at'             => now(),
            'opening_balance'       => round(max(0, $openingBalance), 2),
            'cash_in_total'         => 0,
            'cash_out_total'        => 0,
            'cash_sales_total'      => 0,
            'non_cash_sales_total'  => 0,
            'expected_closing_cash' => round(max(0, $openingBalance), 2),
            'opening_notes'         => $notes,
            'company_id'            => $register->company_id ?? auth()->user()?->default_company_id,
        ]);
    }

    public function recordCashMovement(
        PosSession $session,
        string $type,
        float $amount,
        string $reason,
        ?int $userId = null
    ): PosCashMovement {
        if ($session->status !== PosSession::STATUS_OPEN) {
            throw new InvalidArgumentException('Sesi kasir sudah ditutup.');
        }

        $normalizedType = $type === PosCashMovement::TYPE_OUT
            ? PosCashMovement::TYPE_OUT
            : PosCashMovement::TYPE_IN;

        $cleanAmount = round(max(0, $amount), 2);

        if ($cleanAmount <= 0) {
            throw new InvalidArgumentException('Nominal kas masuk/keluar harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($session, $normalizedType, $cleanAmount, $reason, $userId): PosCashMovement {
            $movement = PosCashMovement::query()->create([
                'pos_session_id' => $session->id,
                'type'           => $normalizedType,
                'amount'         => $cleanAmount,
                'reason'         => trim($reason) !== '' ? trim($reason) : ($normalizedType === PosCashMovement::TYPE_IN ? 'Kas Masuk' : 'Kas Keluar'),
                'user_id'        => $userId ?? auth()->id(),
            ]);

            $this->recalculateSessionTotals($session);

            return $movement;
        });
    }

    public function recalculateSessionTotals(PosSession $session): PosSession
    {
        $session->refresh();

        $cashIn = (float) $session->cashMovements()
            ->where('type', PosCashMovement::TYPE_IN)
            ->sum('amount');

        $cashOut = (float) $session->cashMovements()
            ->where('type', PosCashMovement::TYPE_OUT)
            ->sum('amount');

        $paidOrderIds = $session->orders()
            ->where('status', PosOrder::STATUS_PAID)
            ->pluck('id');

        $totalChangeGiven = (float) $session->orders()
            ->where('status', PosOrder::STATUS_PAID)
            ->sum('change_amount');

        $grossCashPayments = (float) PosOrderPayment::query()
            ->whereIn('pos_order_id', $paidOrderIds)
            ->where('payment_type', PosPaymentMethod::TYPE_CASH)
            ->sum('amount');

        $netCashSales = max(0, round($grossCashPayments - $totalChangeGiven, 2));

        $nonCashSales = (float) PosOrderPayment::query()
            ->whereIn('pos_order_id', $paidOrderIds)
            ->where('payment_type', '!=', PosPaymentMethod::TYPE_CASH)
            ->sum('amount');

        $expectedCash = round(
            (float) $session->opening_balance + $netCashSales + $cashIn - $cashOut,
            2
        );

        $session->update([
            'cash_in_total'         => round($cashIn, 2),
            'cash_out_total'        => round($cashOut, 2),
            'cash_sales_total'      => round($netCashSales, 2),
            'non_cash_sales_total'  => round($nonCashSales, 2),
            'expected_closing_cash' => $expectedCash,
        ]);

        return $session->refresh();
    }

    public function closeSession(
        PosSession $session,
        float $closingCashCounted,
        ?string $closingNotes = null
    ): PosSession {
        return DB::transaction(function () use ($session, $closingCashCounted, $closingNotes): PosSession {
            $session = $this->recalculateSessionTotals($session);

            $counted = round(max(0, $closingCashCounted), 2);
            $difference = round($counted - (float) $session->expected_closing_cash, 2);

            $session->update([
                'status'               => PosSession::STATUS_CLOSED,
                'closed_at'            => now(),
                'closing_cash_counted' => $counted,
                'cash_difference'      => $difference,
                'closing_notes'        => $closingNotes,
            ]);

            return $session->refresh();
        });
    }

    /**
     * @param  array<int, array{product_id: int|null, product_name?: string, barcode?: string|null, sku?: string|null, qty: float|int, unit_price: float|int, discount_amount?: float|int}>  $items
     * @param  array<int, array{payment_method_id: int, amount: float|int, reference?: string|null}>  $payments
     */
    public function checkout(
        PosSession $session,
        array $items,
        array $payments = [],
        ?int $partnerId = null,
        float $orderDiscountAmount = 0,
        ?float $taxRate = null,
        ?string $note = null,
        string $status = PosOrder::STATUS_PAID,
        ?int $cashierId = null
    ): PosOrder {
        if ($session->status !== PosSession::STATUS_OPEN) {
            throw new InvalidArgumentException('Sesi kasir sudah ditutup. Silakan buka sesi baru.');
        }

        if (empty($items)) {
            throw new InvalidArgumentException('Keranjang belanja masih kosong.');
        }

        return DB::transaction(function () use (
            $session,
            $items,
            $payments,
            $partnerId,
            $orderDiscountAmount,
            $taxRate,
            $note,
            $status,
            $cashierId
        ): PosOrder {
            $register = $session->register()->firstOrFail();

            $subtotal = 0.0;
            $preparedLines = [];

            foreach ($items as $item) {
                $qty = round(max(0.01, (float) ($item['qty'] ?? 1)), 2);
                $unitPrice = round(max(0, (float) ($item['unit_price'] ?? 0)), 2);
                $lineDiscount = round(max(0, (float) ($item['discount_amount'] ?? 0)), 2);
                $grossLine = round($qty * $unitPrice, 2);
                $lineDiscount = min($lineDiscount, $grossLine);
                $lineTotal = round($grossLine - $lineDiscount, 2);

                $product = null;
                if (! empty($item['product_id'])) {
                    $product = Product::withTrashed()->withoutGlobalScopes()->find($item['product_id']);
                }

                if (
                    $status === PosOrder::STATUS_PAID
                    && $register->auto_deduct_stock
                    && ! $register->allow_out_of_stock_sale
                    && $product
                ) {
                    $availableQty = $this->getAvailableStock($product->id, $register);

                    if ($availableQty < $qty) {
                        throw new InvalidArgumentException(
                            "Stok produk '{$product->name}' tidak mencukupi (Tersedia: {$availableQty}, Diminta: {$qty})."
                        );
                    }
                }

                $subtotal += $lineTotal;

                $preparedLines[] = [
                    'product_id'      => $product?->id ?? ($item['product_id'] ?? null),
                    'product_name'    => $item['product_name'] ?? $product?->name ?? 'Item POS',
                    'barcode'         => $item['barcode'] ?? $product?->barcode,
                    'sku'             => $item['sku'] ?? $product?->reference,
                    'qty'             => $qty,
                    'unit_price'      => $unitPrice,
                    'discount_amount' => $lineDiscount,
                    'line_total'      => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $cleanOrderDiscount = min(round(max(0, $orderDiscountAmount), 2), $subtotal);
            $taxableBase = round($subtotal - $cleanOrderDiscount, 2);
            $appliedTaxRate = round(max(0, $taxRate ?? (float) $register->default_tax_rate), 2);
            $taxAmount = round($taxableBase * ($appliedTaxRate / 100), 2);
            $totalAmount = round($taxableBase + $taxAmount, 2);

            $preparedPayments = [];
            $paidAmount = 0.0;

            if ($status === PosOrder::STATUS_PAID) {
                foreach ($payments as $paymentRow) {
                    $amount = round(max(0, (float) ($paymentRow['amount'] ?? 0)), 2);

                    if ($amount <= 0) {
                        continue;
                    }

                    $method = PosPaymentMethod::query()->find($paymentRow['payment_method_id'] ?? null);

                    if (! $method) {
                        $method = PosPaymentMethod::query()
                            ->where('type', PosPaymentMethod::TYPE_CASH)
                            ->first();
                    }

                    $paidAmount += $amount;

                    $preparedPayments[] = [
                        'pos_payment_method_id' => $method?->id,
                        'payment_method_name'   => $method?->name ?? 'Tunai (Cash)',
                        'payment_type'          => $method?->type ?? PosPaymentMethod::TYPE_CASH,
                        'amount'                => $amount,
                        'reference'             => $paymentRow['reference'] ?? null,
                    ];
                }

                $paidAmount = round($paidAmount, 2);

                if ($paidAmount + 0.001 < $totalAmount) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Jumlah pembayaran (Rp %s) kurang dari total tagihan (Rp %s).',
                            number_format($paidAmount, 0, ',', '.'),
                            number_format($totalAmount, 0, ',', '.')
                        )
                    );
                }
            }

            $changeAmount = $status === PosOrder::STATUS_PAID
                ? round(max(0, $paidAmount - $totalAmount), 2)
                : 0.0;

            $nextOrderNo = PosOrder::query()->count() + 1;
            $orderName = sprintf('INV-POS-%s-%04d', now()->format('Ymd'), $nextOrderNo);

            while (PosOrder::query()->where('name', $orderName)->exists()) {
                $nextOrderNo++;
                $orderName = sprintf('INV-POS-%s-%04d', now()->format('Ymd'), $nextOrderNo);
            }

            $order = PosOrder::query()->create([
                'name'            => $orderName,
                'pos_session_id'  => $session->id,
                'pos_register_id' => $register->id,
                'partner_id'      => $partnerId,
                'cashier_id'      => $cashierId ?? auth()->id() ?? $session->user_id,
                'status'          => $status,
                'subtotal'        => $subtotal,
                'discount_amount' => $cleanOrderDiscount,
                'tax_rate'        => $appliedTaxRate,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $totalAmount,
                'paid_amount'     => $paidAmount,
                'change_amount'   => $changeAmount,
                'stock_deducted'  => false,
                'note'            => $note,
                'company_id'      => $session->company_id ?? $register->company_id,
            ]);

            foreach ($preparedLines as $lineData) {
                $order->lines()->create($lineData);
            }

            foreach ($preparedPayments as $paymentData) {
                $order->payments()->create($paymentData);
            }

            if ($status === PosOrder::STATUS_PAID && $register->auto_deduct_stock) {
                $this->deductStockForOrder($order, $register);
            }

            $this->recalculateSessionTotals($session);

            return $order->load(['lines', 'payments', 'partner', 'register', 'session', 'cashier']);
        });
    }

    public function refundOrder(PosOrder $order, ?string $reason = null): PosOrder
    {
        if ($order->status === PosOrder::STATUS_REFUNDED) {
            return $order;
        }

        return DB::transaction(function () use ($order, $reason): PosOrder {
            $register = $order->register()->first();

            if ($order->stock_deducted && $register) {
                $this->restoreStockForOrder($order, $register);
            }

            $updatedNote = trim(($order->note ? $order->note."\n" : '').'REFUND: '.($reason ?: 'Retur Pelanggan'));

            $order->update([
                'status'         => PosOrder::STATUS_REFUNDED,
                'stock_deducted' => false,
                'note'           => $updatedNote,
            ]);

            if ($order->session) {
                $this->recalculateSessionTotals($order->session);
            }

            return $order->refresh();
        });
    }

    public function resolveStockLocationId(PosRegister $register): ?int
    {
        if ($register->stock_location_id) {
            return (int) $register->stock_location_id;
        }

        if ($register->warehouse_id && Schema::hasTable('inventories_warehouses')) {
            $warehouse = Warehouse::withTrashed()->withoutGlobalScopes()->find($register->warehouse_id);

            if ($warehouse?->lot_stock_location_id) {
                return (int) $warehouse->lot_stock_location_id;
            }
        }

        if (Schema::hasTable('inventories_locations')) {
            $locationId = Location::withTrashed()
                ->withoutGlobalScopes()
                ->where('type', 'internal')
                ->value('id');

            return $locationId ? (int) $locationId : null;
        }

        return null;
    }

    public function getAvailableStock(int $productId, ?PosRegister $register = null): float
    {
        if (! Schema::hasTable('inventories_product_quantities')) {
            return 0.0;
        }

        $query = ProductQuantity::query()
            ->withoutGlobalScopes()
            ->where('product_id', $productId);

        $locationId = $register ? $this->resolveStockLocationId($register) : null;

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return round((float) $query->sum('quantity'), 2);
    }

    protected function deductStockForOrder(PosOrder $order, PosRegister $register): void
    {
        if (! Schema::hasTable('inventories_product_quantities')) {
            return;
        }

        $locationId = $this->resolveStockLocationId($register);

        if (! $locationId) {
            return;
        }

        foreach ($order->lines as $line) {
            if (! $line->product_id) {
                continue;
            }

            $productQuantity = ProductQuantity::query()
                ->withoutGlobalScopes()
                ->where('product_id', $line->product_id)
                ->where('location_id', $locationId)
                ->first();

            if ($productQuantity) {
                $newQty = round((float) $productQuantity->quantity - (float) $line->qty, 2);
                $productQuantity->update([
                    'quantity' => $newQty,
                ]);
            } else {
                ProductQuantity::query()->create([
                    'product_id'        => $line->product_id,
                    'location_id'       => $locationId,
                    'quantity'          => round(0 - (float) $line->qty, 2),
                    'reserved_quantity' => 0,
                    'company_id'        => $order->company_id ?? $register->company_id,
                    'creator_id'        => $order->cashier_id ?? auth()->id(),
                ]);
            }
        }

        $order->update(['stock_deducted' => true]);
    }

    protected function restoreStockForOrder(PosOrder $order, PosRegister $register): void
    {
        if (! Schema::hasTable('inventories_product_quantities')) {
            return;
        }

        $locationId = $this->resolveStockLocationId($register);

        if (! $locationId) {
            return;
        }

        foreach ($order->lines as $line) {
            if (! $line->product_id) {
                continue;
            }

            $productQuantity = ProductQuantity::query()
                ->withoutGlobalScopes()
                ->where('product_id', $line->product_id)
                ->where('location_id', $locationId)
                ->first();

            if ($productQuantity) {
                $productQuantity->update([
                    'quantity' => round((float) $productQuantity->quantity + (float) $line->qty, 2),
                ]);
            } else {
                ProductQuantity::query()->create([
                    'product_id'        => $line->product_id,
                    'location_id'       => $locationId,
                    'quantity'          => round((float) $line->qty, 2),
                    'reserved_quantity' => 0,
                    'company_id'        => $order->company_id ?? $register->company_id,
                    'creator_id'        => auth()->id() ?? $order->cashier_id,
                ]);
            }
        }
    }

    public function buildWhatsappReceiptText(PosOrder $order): string
    {
        $order->loadMissing(['lines', 'payments', 'register', 'partner', 'cashier']);

        $storeName = $order->register?->name ?? 'auraERP POS';
        $linesText = [];

        foreach ($order->lines as $line) {
            $qty = rtrim(rtrim(number_format((float) $line->qty, 2, ',', '.'), '0'), ',');
            $unitPrice = number_format((float) $line->unit_price, 0, ',', '.');
            $lineTotal = number_format((float) $line->line_total, 0, ',', '.');
            $linesText[] = "• {$line->product_name} ({$qty} x Rp {$unitPrice}) = Rp {$lineTotal}";
        }

        $paymentLines = [];
        foreach ($order->payments as $payment) {
            $paymentLines[] = $payment->payment_method_name.': Rp '.number_format((float) $payment->amount, 0, ',', '.');
        }

        $message = [
            "*STRUK PEMBELIAN - {$storeName}*",
            "No. Struk: *{$order->name}*",
            'Tanggal: '.$order->created_at?->format('d/m/Y H:i'),
            'Kasir: '.($order->cashier?->name ?? '-'),
            'Pelanggan: '.($order->partner?->name ?? 'Umum'),
            '--------------------------------',
            implode("\n", $linesText),
            '--------------------------------',
            'Subtotal: Rp '.number_format((float) $order->subtotal, 0, ',', '.'),
        ];

        if ((float) $order->discount_amount > 0) {
            $message[] = 'Diskon: -Rp '.number_format((float) $order->discount_amount, 0, ',', '.');
        }

        if ((float) $order->tax_amount > 0) {
            $message[] = 'Pajak ('.rtrim(rtrim(number_format((float) $order->tax_rate, 2, ',', '.'), '0'), ',').'%): Rp '.number_format((float) $order->tax_amount, 0, ',', '.');
        }

        $message[] = '*TOTAL: Rp '.number_format((float) $order->total_amount, 0, ',', '.').'*';
        $message[] = 'Dibayar: Rp '.number_format((float) $order->paid_amount, 0, ',', '.');
        $message[] = 'Kembalian: Rp '.number_format((float) $order->change_amount, 0, ',', '.');

        if (! empty($paymentLines)) {
            $message[] = 'Metode: '.implode(', ', $paymentLines);
        }

        $footer = $order->register?->receipt_footer ?: 'Terima kasih atas kunjungan Anda!';
        $message[] = '--------------------------------';
        $message[] = $footer;

        return implode("\n", $message);
    }

    public function buildWhatsappReceiptUrl(PosOrder $order, ?string $customPhone = null): string
    {
        $text = rawurlencode($this->buildWhatsappReceiptText($order));
        $rawPhone = $customPhone ?: ($order->partner?->mobile ?: $order->partner?->phone);

        if (! $rawPhone) {
            return "https://wa.me/?text={$text}";
        }

        $digits = preg_replace('/\D+/', '', $rawPhone) ?: '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits !== ''
            ? "https://wa.me/{$digits}?text={$text}"
            : "https://wa.me/?text={$text}";
    }
}
