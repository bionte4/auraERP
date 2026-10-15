<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PosRegister extends Model
{
    protected $table = 'pos_registers';

    protected $fillable = [
        'name',
        'code',
        'warehouse_id',
        'stock_location_id',
        'default_tax_rate',
        'auto_deduct_stock',
        'allow_out_of_stock_sale',
        'receipt_paper_size',
        'receipt_header',
        'receipt_footer',
        'is_active',
        'company_id',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'default_tax_rate'        => 'decimal:2',
            'auto_deduct_stock'       => 'boolean',
            'allow_out_of_stock_sale' => 'boolean',
            'is_active'               => 'boolean',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function stockLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'stock_location_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PosSession::class, 'pos_register_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(PosOrder::class, 'pos_register_id');
    }
}
