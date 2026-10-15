<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PosSession extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $table = 'pos_sessions';

    protected $fillable = [
        'name',
        'pos_register_id',
        'user_id',
        'status',
        'opened_at',
        'closed_at',
        'opening_balance',
        'cash_in_total',
        'cash_out_total',
        'cash_sales_total',
        'non_cash_sales_total',
        'expected_closing_cash',
        'closing_cash_counted',
        'cash_difference',
        'opening_notes',
        'closing_notes',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'opened_at'             => 'datetime',
            'closed_at'             => 'datetime',
            'opening_balance'       => 'decimal:2',
            'cash_in_total'         => 'decimal:2',
            'cash_out_total'        => 'decimal:2',
            'cash_sales_total'      => 'decimal:2',
            'non_cash_sales_total'  => 'decimal:2',
            'expected_closing_cash' => 'decimal:2',
            'closing_cash_counted'  => 'decimal:2',
            'cash_difference'       => 'decimal:2',
        ];
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(PosRegister::class, 'pos_register_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(PosCashMovement::class, 'pos_session_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(PosOrder::class, 'pos_session_id');
    }
}
