<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderPayment extends Model
{
    protected $table = 'pos_order_payments';

    protected $fillable = [
        'pos_order_id',
        'pos_payment_method_id',
        'payment_method_name',
        'payment_type',
        'amount',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PosPaymentMethod::class, 'pos_payment_method_id');
    }
}
