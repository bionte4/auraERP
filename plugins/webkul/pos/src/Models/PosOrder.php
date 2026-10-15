<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PosOrder extends Model
{
    public const STATUS_PAID = 'paid';

    public const STATUS_PARKED = 'parked';

    public const STATUS_REFUNDED = 'refunded';

    protected $table = 'pos_orders';

    protected $fillable = [
        'name',
        'pos_session_id',
        'pos_register_id',
        'partner_id',
        'cashier_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'change_amount',
        'stock_deducted',
        'note',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_rate'        => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'paid_amount'     => 'decimal:2',
            'change_amount'   => 'decimal:2',
            'stock_deducted'  => 'boolean',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'pos_register_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PosOrderLine::class, 'pos_order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosOrderPayment::class, 'pos_order_id');
    }
}
