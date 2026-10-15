<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

class PosOrderLine extends Model
{
    protected $table = 'pos_order_lines';

    protected $fillable = [
        'pos_order_id',
        'product_id',
        'product_name',
        'barcode',
        'sku',
        'qty',
        'unit_price',
        'discount_amount',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'qty'             => 'decimal:2',
            'unit_price'      => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'line_total'      => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
