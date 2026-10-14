<?php

namespace Webkul\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\Maintenance\Enums\EquipmentMovementType;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class EquipmentMovement extends Model
{
    protected $table = 'maintenance_equipment_movements';

    protected $fillable = [
        'equipment_id',
        'movement_type',
        'movement_date',
        'reference',
        'from_employee_id',
        'to_employee_id',
        'from_project_id',
        'to_project_id',
        'from_customer_id',
        'to_customer_id',
        'from_location',
        'to_location',
        'replacement_equipment_id',
        'notes',
        'company_id',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'movement_type' => EquipmentMovementType::class,
            'movement_date' => 'date',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id')->withTrashed();
    }

    public function replacementEquipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'replacement_equipment_id')->withTrashed();
    }

    public function fromEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function fromProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'from_project_id');
    }

    public function toProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'to_project_id');
    }

    public function fromCustomer(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'from_customer_id');
    }

    public function toCustomer(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'to_customer_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $movement): void {
            $movement->creator_id ??= Auth::id();
            $movement->movement_date ??= now()->toDateString();

            $equipment = $movement->equipment;

            if ($equipment) {
                $movement->company_id ??= $equipment->company_id;
                $movement->from_employee_id ??= $equipment->employee_id;
                $movement->from_project_id ??= $equipment->project_id;
                $movement->from_customer_id ??= $equipment->customer_id;
                $movement->from_location ??= $equipment->location;
            }
        });

        static::created(function (self $movement): void {
            $movement->applyToEquipment();
        });
    }

    public function applyToEquipment(): void
    {
        $equipment = $this->equipment()->first();

        if (! $equipment) {
            return;
        }

        $type = $this->movement_type instanceof EquipmentMovementType
            ? $this->movement_type
            : EquipmentMovementType::tryFrom((string) $this->movement_type);

        $updates = [];

        if ($type === EquipmentMovementType::Return) {
            $updates['employee_id'] = null;
            $updates['project_id'] = null;
            $updates['customer_id'] = null;
            $updates['location'] = $this->to_location ?? $equipment->location;
        } elseif ($type === EquipmentMovementType::Scrap) {
            $updates['employee_id'] = null;
            $updates['project_id'] = null;
            $updates['customer_id'] = null;
            $updates['scraped_at'] = $this->movement_date ?? now()->toDateString();

            if ($this->to_location !== null) {
                $updates['location'] = $this->to_location;
            }
        } elseif ($type === EquipmentMovementType::Replacement) {
            $replacement = $this->replacementEquipment()->first();

            if ($replacement) {
                $targetEmployeeId = $this->to_employee_id ?? $equipment->employee_id;
                $targetProjectId = $this->to_project_id ?? $equipment->project_id;
                $targetCustomerId = $this->to_customer_id ?? $equipment->customer_id;
                $targetLocation = $equipment->location ?? $this->from_location ?? $this->to_location;
                $targetOwnerId = $targetEmployeeId
                    ? Employee::withoutGlobalScopes()->find($targetEmployeeId)?->user_id
                    : $equipment->owner_user_id;

                $replacement->forceFill([
                    'employee_id'   => $targetEmployeeId,
                    'owner_user_id' => $targetOwnerId ?? $replacement->owner_user_id,
                    'project_id'    => $targetProjectId,
                    'customer_id'   => $targetCustomerId,
                    'location'      => $targetLocation,
                    'assigned_at'   => $this->movement_date ?? now()->toDateString(),
                ])->save();
            }

            $updates['employee_id'] = null;
            $updates['project_id'] = null;
            $updates['customer_id'] = null;

            if ($this->to_location !== null) {
                $updates['location'] = $this->to_location;
            }
        } else {
            if ($this->to_employee_id !== null) {
                $updates['employee_id'] = $this->to_employee_id;
                $userId = Employee::withoutGlobalScopes()->find($this->to_employee_id)?->user_id;

                if ($userId) {
                    $updates['owner_user_id'] = $userId;
                }
            }

            if ($this->to_project_id !== null) {
                $updates['project_id'] = $this->to_project_id;
            }

            if ($this->to_customer_id !== null) {
                $updates['customer_id'] = $this->to_customer_id;
            }

            if ($this->to_location !== null) {
                $updates['location'] = $this->to_location;
            }

            $updates['assigned_at'] = $this->movement_date ?? now()->toDateString();
        }

        if ($updates !== []) {
            $equipment->forceFill($updates)->save();
        }

        if (method_exists($equipment, 'addMessage')) {
            $typeLabel = $type?->getLabel() ?? (string) $this->movement_type;
            $summaryParts = [
                '<strong>'.e($typeLabel).'</strong> ('.e($this->movement_date?->format('d M Y') ?? now()->format('d M Y')).')',
            ];

            if ($this->toEmployee?->name || $this->fromEmployee?->name) {
                $summaryParts[] = e(__('maintenance::filament/resources/equipment.movements.log.employee', [
                    'from' => $this->fromEmployee?->name ?? '—',
                    'to'   => $this->toEmployee?->name ?? '—',
                ]));
            }

            if ($this->toProject?->name || $this->fromProject?->name) {
                $summaryParts[] = e(__('maintenance::filament/resources/equipment.movements.log.project', [
                    'from' => $this->fromProject?->name ?? '—',
                    'to'   => $this->toProject?->name ?? '—',
                ]));
            }

            if ($this->toLocation || $this->from_location || $this->to_location) {
                $summaryParts[] = e(__('maintenance::filament/resources/equipment.movements.log.location', [
                    'from' => $this->from_location ?: '—',
                    'to'   => $this->to_location ?: '—',
                ]));
            }

            if ($this->replacementEquipment?->name) {
                $summaryParts[] = e(__('maintenance::filament/resources/equipment.movements.log.replacement', [
                    'equipment' => $this->replacementEquipment->name,
                ]));
            }

            if ($this->notes) {
                $summaryParts[] = e($this->notes);
            }

            $equipment->addMessage([
                'type'        => 'note',
                'name'        => $equipment->name,
                'subject'     => $typeLabel,
                'body'        => '<p>'.implode('<br>', $summaryParts).'</p>',
                'is_internal' => true,
            ]);
        }
    }
}
