<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $table = 'employees_employee_contracts';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_EXTENDED = 'extended';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'employee_id',
        'sequence',
        'employment_type_id',
        'contract_number',
        'start_date',
        'end_date',
        'status',
        'source',
        'notes',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'sequence'   => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function label(): string
    {
        return __('employees::models/employee-contract.label', ['sequence' => $this->sequence]);
    }

    /**
     * Whole months covered by this contract (start..end inclusive-ish).
     * Open-ended contracts count until today.
     */
    public function durationInMonths(): int
    {
        if (! $this->start_date) {
            return 0;
        }

        $end = $this->end_date ?? now();

        return (int) $this->start_date->copy()->startOfDay()->diffInMonths($end->copy()->startOfDay());
    }

    public function isPkwt(): bool
    {
        $type = $this->employmentType;

        if (! $type) {
            return false;
        }

        $haystack = mb_strtolower(($type->code ?? '').' '.($type->name ?? ''));

        return str_contains($haystack, 'pkwt') || str_contains($haystack, 'contract');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $contract): void {
            $contract->creator_id ??= Auth::id();
            $contract->company_id ??= $contract->employee?->company_id;

            if (! $contract->sequence) {
                $contract->sequence = (int) static::query()
                    ->where('employee_id', $contract->employee_id)
                    ->max('sequence') + 1;
            }
        });

        static::saved(function (self $contract): void {
            // A newer active contract supersedes older active ones.
            if ($contract->status === self::STATUS_ACTIVE) {
                static::query()
                    ->where('employee_id', $contract->employee_id)
                    ->where('status', self::STATUS_ACTIVE)
                    ->where('sequence', '<', $contract->sequence)
                    ->whereKeyNot($contract->getKey())
                    ->update(['status' => self::STATUS_EXTENDED]);
            }

            // The latest active contract drives the employee's running fields.
            if ($contract->status !== self::STATUS_ACTIVE || ! $contract->employee) {
                return;
            }

            $latest = $contract->employee->contracts()
                ->where('status', self::STATUS_ACTIVE)
                ->orderByDesc('sequence')
                ->first();

            if ($latest && $latest->is($contract)) {
                $contract->employee->forceFill([
                    'contract_end_date'  => $contract->end_date,
                    'employment_type_id' => $contract->employment_type_id ?? $contract->employee->employment_type_id,
                ])->saveQuietly();
            }
        });
    }
}
