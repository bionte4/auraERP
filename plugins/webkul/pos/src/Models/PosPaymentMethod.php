<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\Company;

class PosPaymentMethod extends Model
{
    public const TYPE_CASH = 'cash';

    public const TYPE_QRIS = 'qris';

    public const TYPE_TRANSFER = 'transfer';

    public const TYPE_CARD = 'card';

    protected $table = 'pos_payment_methods';

    protected $fillable = [
        'name',
        'code',
        'type',
        'account_number',
        'sort',
        'is_active',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'sort'      => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            self::TYPE_CASH     => 'Tunai (Cash)',
            self::TYPE_QRIS     => 'QRIS / E-Wallet',
            self::TYPE_TRANSFER => 'Transfer Bank',
            self::TYPE_CARD     => 'Kartu Debit / Kredit (EDC)',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
