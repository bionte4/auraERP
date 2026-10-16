<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Webkul\Security\Enums\SecurityAuditEvent;

class AuditTrail extends Model
{
    public $timestamps = false;

    protected $table = 'security_audit_trails';

    protected $fillable = [
        'event',
        'description',
        'actor_type',
        'actor_id',
        'actor_label',
        'subject_type',
        'subject_id',
        'subject_label',
        'ip_address',
        'user_agent',
        'guard',
        'properties',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event'      => SecurityAuditEvent::class,
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
