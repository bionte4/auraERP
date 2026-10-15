<x-filament-panels::page>
    @php
        $registers = $this->getRegisters();
        $activeSession = $this->getActiveSession();
        $activeRegister = $this->getActiveRegister();
        $categories = $this->getCategories();
        $products = $this->getProducts();
        $customers = $this->getCustomers();
        $paymentMethods = $this->getPaymentMethods();
        $parkedOrders = $this->getParkedOrders();
        $recentOrders = $this->getRecentSessionOrders();
        $receiptOrder = $this->getReceiptOrder();
        $subtotal = $this->getCartSubtotal();
        $taxAmount = $this->getTaxAmount();
        $cartTotal = $this->getCartTotal();
        $totalPaid = $this->getTotalPaidInput();
        $changeAmount = $this->getChangeAmount();
    @endphp

    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #pos-thermal-receipt-area,
            #pos-thermal-receipt-area * {
                visibility: visible !important;
            }
            #pos-thermal-receipt-area {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                padding: 8px !important;
                box-shadow: none !important;
                border: none !important;
                background: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="space-y-4">
        {{-- Top Status Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Mesin Kasir (Register)</label>
                    <select
                        wire:model.live="selectedRegisterId"
                        class="mt-1 rounded-lg border-gray-300 bg-gray-50 text-sm font-semibold text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        @foreach ($registers as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->name }} ({{ $reg->code }})</option>
                        @endforeach
                    </select>
                </div>

                @if ($activeSession)
                    <div class="rounded-lg bg-emerald-50 px-3 py-2 text-xs dark:bg-emerald-950/60">
                        <div class="font-bold text-emerald-700 dark:text-emerald-300">
                            Sesi Aktif: {{ $activeSession->name }}
                        </div>
                        <div class="text-emerald-600 dark:text-emerald-400">
                            Kasir: {{ $activeSession->user?->name ?? auth()->user()?->name }} |
                            Saldo Kas Diharapkan: <strong>Rp {{ number_format((float) $activeSession->expected_closing_cash, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                        Belum ada Sesi Kasir (Shift) yang terbuka pada register ini.
                    </div>
                @endif
            </div>

            @if ($activeSession)
                <div class="flex flex-wrap items-center gap-2">
                    <x-filament::button
                        color="info"
                        size="sm"
                        icon="heroicon-o-arrows-right-left"
                        wire:click="$set('showCashMovementModal', true)"
                    >
                        Kas Masuk / Keluar
                    </x-filament::button>

                    <x-filament::button
                        color="danger"
                        size="sm"
                        icon="heroicon-o-lock-closed"
                        wire:click="prepareCloseSessionModal"
                    >
                        Tutup Kasir (Z-Report)
                    </x-filament::button>
                </div>
            @endif
        </div>

        {{-- Open Session Card if no session is open --}}
        @if (! $activeSession)
            <div class="mx-auto max-w-xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Buka Sesi Kasir Baru (Shift)
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Masukkan modal awal uang tunai di laci kasir sebelum memulai transaksi penjualan.
                </p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Saldo Awal Kas di Laci (Rp)
                        </label>
                        <input
                            type="number"
                            min="0"
                            step="1000"
                            wire:model="openingBalanceInput"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-base font-semibold shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Contoh: 200000"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Catatan Buka Shift (Opsional)
                        </label>
                        <input
                            type="text"
                            wire:model="openingNotesInput"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Misal: Shift Pagi - Uang pecahan lengkap"
                        />
                    </div>

                    <x-filament::button
                        color="primary"
                        size="lg"
                        icon="heroicon-o-play"
                        class="w-full justify-center"
                        wire:click="openSession"
                    >
                        Buka Sesi Kasir Sekarang
                    </x-filament::button>
                </div>
            </div>
        @else
            {{-- Main Cashier Grid --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                {{-- Left Column: Barcode Scanner, Filter, Product Grid (7 cols) --}}
                <div class="space-y-4 lg:col-span-7">
                    {{-- Barcode & Product Search Bar --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">
                                    Scan Barcode / SKU (Tekan Enter)
                                </label>
                                <div class="mt-1 flex gap-2">
                                    <input
                                        type="text"
                                        wire:model="barcodeInput"
                                        wire:keydown.enter.prevent="scanBarcode"
                                        autofocus
                                        placeholder="Scan barcode atau ketik kode..."
                                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                    />
                                    <x-filament::button color="primary" size="sm" wire:click="scanBarcode">
                                        Tambah
                                    </x-filament::button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Cari Nama Produk / SKU
                                </label>
                                <input
                                    type="text"
                                    wire:model.live.debounce.250ms="searchQuery"
                                    placeholder="Ketik nama barang..."
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                />
                            </div>
                        </div>

                        {{-- Category Pills --}}
                        @if ($categories->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                <button
                                    type="button"
                                    wire:click="$set('selectedCategoryId', null)"
                                    class="rounded-full px-3 py-1 text-xs font-medium transition {{ $selectedCategoryId === null ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}"
                                >
                                    Semua Kategori
                                </button>
                                @foreach ($categories as $cat)
                                    <button
                                        type="button"
                                        wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                                        class="rounded-full px-3 py-1 text-xs font-medium transition {{ $selectedCategoryId === $cat->id ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}"
                                    >
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Product Cards --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="mb-3 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                Daftar Produk (Klik untuk masuk keranjang)
                            </h4>
                            <span class="text-xs text-gray-500">
                                Menampilkan {{ $products->count() }} produk
                            </span>
                        </div>

                        @if ($products->isEmpty())
                            <div class="py-10 text-center text-sm text-gray-500">
                                Tidak ada produk ditemukan. Pastikan produk sudah dibuat di menu Produk / Inventory.
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                                @foreach ($products as $prod)
                                    <button
                                        type="button"
                                        wire:click="addToCart({{ $prod['id'] }})"
                                        class="group flex flex-col justify-between rounded-xl border border-gray-200 bg-gray-50/60 p-3 text-left transition hover:border-primary-500 hover:bg-primary-50/40 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800/70 dark:hover:border-primary-500"
                                    >
                                        <div>
                                            <div class="flex items-start justify-between gap-1">
                                                <span class="line-clamp-2 text-xs font-bold text-gray-900 group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                                                    {{ $prod['name'] }}
                                                </span>
                                            </div>
                                            @if ($prod['sku'] || $prod['barcode'])
                                                <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                    {{ $prod['sku'] ?: $prod['barcode'] }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 flex items-end justify-between border-t border-gray-200/80 pt-2 dark:border-gray-700">
                                            <span class="text-xs font-extrabold text-primary-600 dark:text-primary-400">
                                                Rp {{ number_format($prod['price'], 0, ',', '.') }}
                                            </span>
                                            <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $prod['available_stock'] > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                                Stok: {{ rtrim(rtrim(number_format($prod['available_stock'], 2, ',', '.'), '0'), ',') }}
                                            </span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Parked Orders & Recent Session Transactions --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Parked / Hold Bills --}}
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                Pesanan Ditahan / Hold Bill ({{ $parkedOrders->count() }})
                            </h4>
                            <div class="mt-2 space-y-2">
                                @forelse ($parkedOrders as $parked)
                                    <div class="flex items-center justify-between rounded-lg border border-amber-200 bg-amber-50/50 px-3 py-2 text-xs dark:border-amber-900 dark:bg-amber-950/30">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $parked->name }}</div>
                                            <div class="text-gray-500">Rp {{ number_format((float) $parked->total_amount, 0, ',', '.') }} | {{ $parked->note }}</div>
                                        </div>
                                        <x-filament::button
                                            size="xs"
                                            color="warning"
                                            wire:click="recallParkedOrder({{ $parked->id }})"
                                        >
                                            Panggil
                                        </x-filament::button>
                                    </div>
                                @empty
                                    <div class="py-3 text-xs text-gray-400">Tidak ada transaksi yang ditahan.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Recent Paid Orders in this Session --}}
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Transaksi Terakhir Sesi Ini
                            </h4>
                            <div class="mt-2 space-y-2">
                                @forelse ($recentOrders as $recent)
                                    <div class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-xs dark:border-gray-800 dark:bg-gray-800/60">
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $recent->name }}</div>
                                            <div class="text-gray-500">
                                                {{ $recent->created_at?->format('H:i') }} • Rp {{ number_format((float) $recent->total_amount, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <x-filament::button
                                            size="xs"
                                            color="gray"
                                            icon="heroicon-o-printer"
                                            wire:click="openReceipt({{ $recent->id }})"
                                        >
                                            Struk
                                        </x-filament::button>
                                    </div>
                                @empty
                                    <div class="py-3 text-xs text-gray-400">Belum ada transaksi selesai pada sesi ini.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Cart, Customer, Multi-Payment & Checkout (5 cols) --}}
                <div class="space-y-4 lg:col-span-5">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        {{-- Customer Selection --}}
                        <div class="flex items-end gap-2 pb-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    Pelanggan (Opsional - Untuk Kirim Struk WhatsApp)
                                </label>
                                <select
                                    wire:model.live="selectedPartnerId"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="">-- Pelanggan Umum (Walk-in) --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->id }}">
                                            {{ $cust->name }} {{ ($cust->mobile || $cust->phone) ? '('.($cust->mobile ?: $cust->phone).')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-filament::button
                                size="sm"
                                color="gray"
                                icon="heroicon-o-user-plus"
                                wire:click="$set('showQuickCustomerModal', true)"
                            >
                                + Pelanggan
                            </x-filament::button>
                        </div>

                        {{-- Shopping Cart Items --}}
                        <div class="mt-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    Keranjang Belanja ({{ count($cart) }} item)
                                </h4>
                                @if (! empty($cart))
                                    <button
                                        type="button"
                                        wire:click="clearCart"
                                        class="text-xs font-semibold text-red-600 hover:underline"
                                    >
                                        Kosongkan
                                    </button>
                                @endif
                            </div>

                            <div class="mt-2 max-h-72 space-y-2 overflow-y-auto pr-1">
                                @forelse ($cart as $key => $item)
                                    @php
                                        $lineGross = round(((float) $item['qty']) * ((float) $item['unit_price']), 2);
                                        $lineDisc = min($lineGross, max(0, (float) $item['discount_amount']));
                                        $lineNet = round($lineGross - $lineDisc, 2);
                                    @endphp
                                    <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-2.5 text-xs dark:border-gray-700 dark:bg-gray-800/60">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $item['product_name'] }}</div>
                                                <div class="text-[11px] text-gray-500">
                                                    @ Rp {{ number_format((float) $item['unit_price'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-extrabold text-gray-900 dark:text-white">
                                                    Rp {{ number_format($lineNet, 0, ',', '.') }}
                                                </div>
                                                <button
                                                    type="button"
                                                    wire:click="removeCartItem('{{ $key }}')"
                                                    class="text-[11px] text-red-500 hover:underline"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-2 grid grid-cols-3 gap-2 items-center">
                                            <div class="flex items-center gap-1">
                                                <button
                                                    type="button"
                                                    wire:click="decrementQty('{{ $key }}')"
                                                    class="rounded bg-gray-200 px-2 py-0.5 font-bold text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-white"
                                                >
                                                    -
                                                </button>
                                                <input
                                                    type="number"
                                                    min="0.01"
                                                    step="1"
                                                    wire:model.live.debounce.300ms="cart.{{ $key }}.qty"
                                                    class="w-14 rounded border-gray-300 px-1.5 py-0.5 text-center text-xs font-bold dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                />
                                                <button
                                                    type="button"
                                                    wire:click="incrementQty('{{ $key }}')"
                                                    class="rounded bg-gray-200 px-2 py-0.5 font-bold text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-white"
                                                >
                                                    +
                                                </button>
                                            </div>

                                            <div>
                                                <label class="block text-[10px] text-gray-500">Harga (Rp)</label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="500"
                                                    wire:model.live.debounce.300ms="cart.{{ $key }}.unit_price"
                                                    class="w-full rounded border-gray-300 px-1.5 py-0.5 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                />
                                            </div>

                                            <div>
                                                <label class="block text-[10px] text-gray-500">Diskon Item (Rp)</label>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="500"
                                                    wire:model.live.debounce.300ms="cart.{{ $key }}.discount_amount"
                                                    class="w-full rounded border-gray-300 px-1.5 py-0.5 text-xs dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-lg border border-dashed border-gray-300 py-8 text-center text-xs text-gray-400 dark:border-gray-700">
                                        Keranjang masih kosong. Scan barcode atau klik produk di sebelah kiri.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Order Summary & Discounts --}}
                        <div class="mt-4 space-y-2 border-t border-gray-200 pt-3 text-xs dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-600 dark:text-gray-400">Diskon Nota (Rp)</span>
                                <input
                                    type="number"
                                    min="0"
                                    step="1000"
                                    wire:model.live.debounce.300ms="orderDiscountAmount"
                                    class="w-32 rounded border-gray-300 px-2 py-1 text-right text-xs font-semibold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                />
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-600 dark:text-gray-400">Pajak / PPN (%)</span>
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.5"
                                        wire:model.live.debounce.300ms="taxRate"
                                        class="w-16 rounded border-gray-300 px-2 py-1 text-right text-xs font-semibold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                    />
                                    <span class="w-24 text-right font-semibold text-gray-800 dark:text-gray-200">
                                        Rp {{ number_format($taxAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between rounded-lg bg-primary-50 px-3 py-2.5 text-base font-extrabold text-primary-900 dark:bg-primary-950/60 dark:text-primary-200">
                                <span>TOTAL TAGIHAN</span>
                                <span>Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Multi-Payment Section --}}
                        <div class="mt-4 border-t border-gray-200 pt-3 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Metode Pembayaran (Multi / Split Bill)
                                </span>
                                <button
                                    type="button"
                                    wire:click="addPaymentLine"
                                    class="text-xs font-bold text-primary-600 hover:underline dark:text-primary-400"
                                >
                                    + Split Pembayaran
                                </button>
                            </div>

                            {{-- Quick Cash Denominations --}}
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <button
                                    type="button"
                                    wire:click="setExactCashAmount"
                                    class="rounded border border-primary-300 bg-primary-50 px-2.5 py-1 text-xs font-bold text-primary-700 hover:bg-primary-100 dark:border-primary-800 dark:bg-primary-950 dark:text-primary-300"
                                >
                                    Uang Pas
                                </button>
                                @foreach ([10000, 20000, 50000, 100000, 200000] as $denom)
                                    <button
                                        type="button"
                                        wire:click="setQuickCashAmount({{ $denom }})"
                                        class="rounded border border-gray-300 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    >
                                        {{ number_format($denom / 1000, 0) }}rb
                                    </button>
                                @endforeach
                            </div>

                            <div class="mt-2 space-y-2">
                                @foreach ($paymentLines as $idx => $pLine)
                                    <div class="grid grid-cols-12 gap-2 items-center">
                                        <div class="col-span-5">
                                            <select
                                                wire:model.live="paymentLines.{{ $idx }}.payment_method_id"
                                                class="block w-full rounded-lg border-gray-300 text-xs font-semibold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            >
                                                @foreach ($paymentMethods as $pm)
                                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-span-4">
                                            <input
                                                type="number"
                                                min="0"
                                                step="1000"
                                                wire:model.live.debounce.250ms="paymentLines.{{ $idx }}.amount"
                                                placeholder="Nominal"
                                                class="block w-full rounded-lg border-gray-300 text-right text-xs font-bold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            />
                                        </div>

                                        <div class="col-span-2">
                                            <input
                                                type="text"
                                                wire:model.live="paymentLines.{{ $idx }}.reference"
                                                placeholder="Ref/No"
                                                class="block w-full rounded-lg border-gray-300 text-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            />
                                        </div>

                                        <div class="col-span-1 text-right">
                                            @if (count($paymentLines) > 1)
                                                <button
                                                    type="button"
                                                    wire:click="removePaymentLine({{ $idx }})"
                                                    class="text-red-500 hover:text-red-700 font-bold text-sm"
                                                >
                                                    ×
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3 flex items-center justify-between rounded-lg bg-gray-100 px-3 py-2 text-xs dark:bg-gray-800">
                                <div>
                                    <span class="text-gray-500">Total Dibayar:</span>
                                    <strong class="ml-1 text-gray-900 dark:text-white">Rp {{ number_format($totalPaid, 0, ',', '.') }}</strong>
                                </div>
                                <div>
                                    <span class="text-gray-500">Kembalian:</span>
                                    <strong class="ml-1 text-sm text-emerald-600 dark:text-emerald-400">Rp {{ number_format($changeAmount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <x-filament::button
                                color="warning"
                                size="lg"
                                icon="heroicon-o-pause"
                                wire:click="parkCurrentOrder"
                                :disabled="empty($cart)"
                            >
                                Tahan (Hold)
                            </x-filament::button>

                            <x-filament::button
                                color="success"
                                size="lg"
                                icon="heroicon-o-check-circle"
                                class="col-span-2 justify-center"
                                wire:click="processCheckout"
                                :disabled="empty($cart)"
                            >
                                Bayar & Cetak Struk
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Cash In / Out --}}
    @if ($showCashMovementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Catat Kas Masuk / Kas Keluar Laci</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Jenis Mutasi</label>
                        <select
                            wire:model="cashMovementType"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="in">Kas Masuk (Tambah Modal / Kembalian)</option>
                            <option value="out">Kas Keluar (Biaya Operasional / Setor Tunai)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Nominal (Rp)</label>
                        <input
                            type="number"
                            min="1"
                            step="1000"
                            wire:model="cashMovementAmount"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm font-bold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Keterangan / Alasan</label>
                        <input
                            type="text"
                            wire:model="cashMovementReason"
                            placeholder="Contoh: Beli plastik kresek / Tambah uang receh"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <x-filament::button color="gray" wire:click="$set('showCashMovementModal', false)">Batal</x-filament::button>
                    <x-filament::button color="primary" wire:click="submitCashMovement">Simpan Mutasi</x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Close Session (Z-Report) --}}
    @if ($showCloseSessionModal && $activeSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    Tutup Sesi Kasir & Rekap Shift (Z-Report) — {{ $activeSession->name }}
                </h3>

                <div class="mt-4 space-y-2 rounded-lg bg-gray-50 p-3.5 text-xs dark:bg-gray-800">
                    <div class="flex justify-between">
                        <span>Saldo Awal Kas:</span>
                        <strong>Rp {{ number_format((float) $activeSession->opening_balance, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Penjualan Tunai (Bersih):</span>
                        <strong class="text-emerald-600">+ Rp {{ number_format((float) $activeSession->cash_sales_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Penjualan Non-Tunai (QRIS/Transfer/EDC):</span>
                        <strong class="text-blue-600">Rp {{ number_format((float) $activeSession->non_cash_sales_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Kas Masuk Manual:</span>
                        <strong class="text-emerald-600">+ Rp {{ number_format((float) $activeSession->cash_in_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Kas Keluar Manual:</span>
                        <strong class="text-red-600">- Rp {{ number_format((float) $activeSession->cash_out_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-sm font-extrabold dark:border-gray-700">
                        <span>Uang Tunai Seharusnya di Laci:</span>
                        <span>Rp {{ number_format((float) $activeSession->expected_closing_cash, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Uang Fisik Aktual Dihitung di Laci (Rp)
                        </label>
                        <input
                            type="number"
                            min="0"
                            step="1000"
                            wire:model="closingCashCountedInput"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-base font-bold dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Catatan Penutupan Shift
                        </label>
                        <input
                            type="text"
                            wire:model="closingNotesInput"
                            placeholder="Catatan serah terima shift..."
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <x-filament::button color="gray" wire:click="$set('showCloseSessionModal', false)">Batal</x-filament::button>
                    <x-filament::button color="danger" wire:click="confirmCloseSession">Tutup Sesi Kasir Sekarang</x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Quick Add Customer --}}
    @if ($showQuickCustomerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Tambah Pelanggan Cepat</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Nama Pelanggan *</label>
                        <input
                            type="text"
                            wire:model="newCustomerName"
                            placeholder="Nama lengkap pelanggan"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Nomor WhatsApp / HP</label>
                        <input
                            type="text"
                            wire:model="newCustomerPhone"
                            placeholder="Contoh: 08123456789"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <x-filament::button color="gray" wire:click="$set('showQuickCustomerModal', false)">Batal</x-filament::button>
                    <x-filament::button color="primary" wire:click="createQuickCustomer">Simpan Pelanggan</x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Thermal Receipt (58mm / 80mm) & WhatsApp Share --}}
    @if ($receiptOrder)
        @php
            $paperWidth = ($receiptOrder->register?->receipt_paper_size ?? '80mm') === '58mm' ? '220px' : '302px';
            $waUrl = $this->getWhatsappUrl();
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-2xl dark:bg-gray-900">
                <div class="no-print mb-4 flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                        Struk Pembelian ({{ $receiptOrder->register?->receipt_paper_size ?? '80mm' }})
                    </h3>
                    <button
                        type="button"
                        wire:click="closeReceipt"
                        class="text-gray-400 hover:text-gray-600 font-bold"
                    >
                        ✕
                    </button>
                </div>

                {{-- Printable Thermal Area --}}
                <div
                    id="pos-thermal-receipt-area"
                    style="width: {{ $paperWidth }}; margin: 0 auto; font-family: 'Courier New', Courier, monospace;"
                    class="rounded border border-gray-200 bg-white p-3 text-[11px] leading-snug text-black shadow-inner"
                >
                    <div class="text-center">
                        <div class="text-xs font-extrabold uppercase">
                            {{ $receiptOrder->register?->name ?? 'auraERP Retail POS' }}
                        </div>
                        @if ($receiptOrder->register?->receipt_header)
                            <div class="whitespace-pre-line text-[10px]">{{ $receiptOrder->register->receipt_header }}</div>
                        @endif
                    </div>

                    <div class="my-2 border-t border-dashed border-black"></div>

                    <div class="space-y-0.5 text-[10px]">
                        <div>No: <strong>{{ $receiptOrder->name }}</strong></div>
                        <div>Tgl: {{ $receiptOrder->created_at?->format('d/m/Y H:i') }}</div>
                        <div>Kasir: {{ $receiptOrder->cashier?->name ?? '-' }}</div>
                        <div>Pelanggan: {{ $receiptOrder->partner?->name ?? 'Umum' }}</div>
                    </div>

                    <div class="my-2 border-t border-dashed border-black"></div>

                    <div class="space-y-1.5">
                        @foreach ($receiptOrder->lines as $rLine)
                            <div>
                                <div class="font-bold">{{ $rLine->product_name }}</div>
                                <div class="flex justify-between text-[10px]">
                                    <span>
                                        {{ rtrim(rtrim(number_format((float) $rLine->qty, 2, ',', '.'), '0'), ',') }} x {{ number_format((float) $rLine->unit_price, 0, ',', '.') }}
                                        @if ((float) $rLine->discount_amount > 0)
                                            (Disc -{{ number_format((float) $rLine->discount_amount, 0, ',', '.') }})
                                        @endif
                                    </span>
                                    <span class="font-bold">{{ number_format((float) $rLine->line_total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="my-2 border-t border-dashed border-black"></div>

                    <div class="space-y-0.5 text-[10px]">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format((float) $receiptOrder->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ((float) $receiptOrder->discount_amount > 0)
                            <div class="flex justify-between">
                                <span>Diskon</span>
                                <span>-Rp {{ number_format((float) $receiptOrder->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ((float) $receiptOrder->tax_amount > 0)
                            <div class="flex justify-between">
                                <span>Pajak ({{ rtrim(rtrim(number_format((float) $receiptOrder->tax_rate, 2, ',', '.'), '0'), ',') }}%)</span>
                                <span>Rp {{ number_format((float) $receiptOrder->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs font-extrabold pt-1">
                            <span>TOTAL</span>
                            <span>Rp {{ number_format((float) $receiptOrder->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @foreach ($receiptOrder->payments as $rPay)
                            <div class="flex justify-between">
                                <span>{{ $rPay->payment_method_name }}</span>
                                <span>Rp {{ number_format((float) $rPay->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-bold">
                            <span>KEMBALIAN</span>
                            <span>Rp {{ number_format((float) $receiptOrder->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="my-2 border-t border-dashed border-black"></div>

                    <div class="text-center text-[10px] whitespace-pre-line">
                        {{ $receiptOrder->register?->receipt_footer ?: 'Terima kasih atas kunjungan Anda!' }}
                    </div>
                </div>

                {{-- WhatsApp & Print Actions --}}
                <div class="no-print mt-4 space-y-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Nomor WhatsApp Tujuan (Opsional)
                        </label>
                        <input
                            type="text"
                            wire:model.live="whatsappPhoneInput"
                            placeholder="Contoh: 08123456789"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <x-filament::button
                            color="primary"
                            icon="heroicon-o-printer"
                            onclick="window.print()"
                        >
                            Cetak Struk Thermal
                        </x-filament::button>

                        @if ($waUrl)
                            <a
                                href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow hover:bg-emerald-500"
                            >
                                Kirim ke WhatsApp
                            </a>
                        @endif
                    </div>

                    <x-filament::button
                        color="gray"
                        class="w-full justify-center"
                        wire:click="closeReceipt"
                    >
                        Transaksi Baru (Tutup)
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
