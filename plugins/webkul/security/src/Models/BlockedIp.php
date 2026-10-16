<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BlockedIp extends Model
{
    protected $table = 'security_blocked_ips';

    protected $fillable = [
        'ip_address',
        'failed_attempts',
        'reason',
        'last_email',
        'blocked_until',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked_until'   => 'datetime',
            'failed_attempts' => 'integer',
        ];
    }

    public function isCurrentlyBlocked(): bool
    {
        return $this->blocked_until instanceof Carbon
            && $this->blocked_until->isFuture();
    }
}
