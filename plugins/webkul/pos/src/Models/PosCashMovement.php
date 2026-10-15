<?php

namespace Webkul\Pos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;

class PosCashMovement extends Model
{
    public const TYPE_IN = 'in';

    public const TYPE_OUT = 'out';

    protected $table = 'pos_cash_movements';

    protected $fillable = [
        'pos_session_id',
        'type',
        'amount',
        'reason',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
