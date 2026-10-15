<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;
use Webkul\TimeOff\Enums\LeaveValidationType;
use Webkul\TimeOff\Enums\RequestDateFromPeriod;
use Webkul\TimeOff\Enums\State;

class Leave extends Model
{
    use BelongsToCompany;
    use HasChatter, HasCustomFields, HasFactory, HasLogActivity;

    public const ACTIVITY_PLAN_PLUGIN = 'time-off';

    protected $table = 'time_off_leaves';

    public function getModelTitle(): string
    {
        return __('time-off::models/leave.title');
    }

    protected $fillable = [
        'user_id',
        'manager_id',
        'holiday_status_id',
        'employee_id',
        'employee_company_id',
        'company_id',
        'department_id',
        'calendar_id',
        'meeting_id',
        'first_approver_id',
        'second_approver_id',
        'creator_id',
        'private_name',
        'attachment',
        'state',
        'duration_display',
        'request_date_from_period',
        'request_date_from',
        'request_date_to',
        'notes',
        'request_unit_half',
        'request_unit_hours',
        'date_from',
        'date_to',
        'number_of_days',
        'number_of_hours',
        'request_hour_from',
        'request_hour_to',
    ];

    public function getLogAttributeLabels(): array
    {
        return [
            'user.name'                => __('time-off::models/leave.log-attributes.user'),
            'manger.name'              => __('time-off::models/leave.log-attributes.manager'),
            'holidayStatus.name'       => __('time-off::models/leave.log-attributes.holiday_status'),
            'employee.name'            => __('time-off::models/leave.log-attributes.employee'),
            'employeeCompany.name'     => __('time-off::models/leave.log-attributes.employee_company'),
            'department.name'          => __('time-off::models/leave.log-attributes.department'),
            'calendar.name'            => __('time-off::models/leave.log-attributes.calendar'),
            'firstApprover.name'       => __('time-off::models/leave.log-attributes.first_approver'),
            'lastApprover.name'        => __('time-off::models/leave.log-attributes.last_approver'),
            'private_name'             => __('time-off::models/leave.log-attributes.description'),
            'state'                    => __('time-off::models/leave.log-attributes.state'),
            'duration_display'         => __('time-off::models/leave.log-attributes.duration_display'),
            'request_date_from_period' => __('time-off::models/leave.log-attributes.request_date_from_period'),
            'request_date_from'        => __('time-off::models/leave.log-attributes.request_date_from'),
            'request_date_to'          => __('time-off::models/leave.log-attributes.request_date_to'),
            'notes'                    => __('time-off::models/leave.log-attributes.notes'),
            'request_unit_half'        => __('time-off::models/leave.log-attributes.request_unit_half'),
            'request_unit_hours'       => __('time-off::models/leave.log-attributes.request_unit_hours'),
            'date_from'                => __('time-off::models/leave.log-attributes.date_from'),
            'date_to'                  => __('time-off::models/leave.log-attributes.date_to'),
            'number_of_days'           => __('time-off::models/leave.log-attributes.number_of_days'),
            'number_of_hours'          => __('time-off::models/leave.log-attributes.number_of_hours'),
            'request_hour_from'        => __('time-off::models/leave.log-attributes.request_hour_from'),
            'request_hour_to'          => __('time-off::models/leave.log-attributes.request_hour_to'),
        ];
    }

    protected $casts = [
        'state'                    => State::class,
        'request_date_from_period' => RequestDateFromPeriod::class,
        'request_date_from'        => 'date',
        'date_from'                => 'date',
        'request_unit_half'        => 'boolean',
        'number_of_hours'          => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function holidayStatus(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'holiday_status_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employeeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'employee_company_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id');
    }

    public function firstApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'first_approver_id');
    }

    public function secondApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'second_approver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function requiresTwoStepApproval(): bool
    {
        $validationType = $this->holidayStatus?->leave_validation_type;

        if ($validationType === LeaveValidationType::BOTH || $validationType === LeaveValidationType::BOTH->value) {
            return true;
        }

        return ! empty($this->first_approver_id) && ! empty($this->second_approver_id);
    }

    public function canBeApprovedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['Admin', 'HRD']) || $user->can('update_time_off_time::off')) {
            return true;
        }

        $approverEmployeeIds = Employee::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('user_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($approverEmployeeIds)) {
            return false;
        }

        if (
            in_array((int) $this->manager_id, $approverEmployeeIds, true)
            || in_array((int) $this->first_approver_id, $approverEmployeeIds, true)
            || in_array((int) $this->second_approver_id, $approverEmployeeIds, true)
        ) {
            return true;
        }

        foreach ($approverEmployeeIds as $empId) {
            $emp = Employee::withoutGlobalScopes()->find($empId);

            if ($emp && in_array((int) $this->employee_id, $emp->getSubordinateEmployeeIds(true), true)) {
                return true;
            }
        }

        return false;
    }

    public function approveBy(?User $user = null): self
    {
        $user ??= Auth::user();

        $actorEmployee = $user
            ? Employee::withoutGlobalScopes()->whereNull('deleted_at')->where('user_id', $user->id)->first()
            : null;

        $currentState = $this->state instanceof State ? $this->state->value : (string) $this->state;

        $isActorSecondApproverOrHigher = $actorEmployee && (
            (int) $this->second_approver_id === (int) $actorEmployee->id
            || ($this->employee && in_array((int) $this->employee_id, $actorEmployee->getSubordinateEmployeeIds(true), true)
                && (int) $this->first_approver_id !== (int) $actorEmployee->id)
        );

        if (
            $currentState === State::CONFIRM->value
            && $this->requiresTwoStepApproval()
            && ! $isActorSecondApproverOrHigher
        ) {
            $this->state = State::VALIDATE_ONE->value;

            if ($actorEmployee) {
                $this->first_approver_id = $actorEmployee->id;
            }
        } else {
            $this->state = State::VALIDATE_TWO->value;

            if ($actorEmployee) {
                if (! $this->first_approver_id) {
                    $this->first_approver_id = $actorEmployee->id;
                } else {
                    $this->second_approver_id = $actorEmployee->id;
                }
            }
        }

        $this->save();

        return $this;
    }

    public function populateApproversFromHierarchy(): void
    {
        if (! $this->employee_id) {
            return;
        }

        $employee = Employee::withoutGlobalScopes()->find($this->employee_id);

        if (! $employee) {
            return;
        }

        $levelOne = $employee->resolveLevelOneApproverEmployee();
        $levelTwo = $employee->resolveLevelTwoApproverEmployee();

        $this->manager_id ??= $levelOne?->id;
        $this->first_approver_id ??= $levelOne?->id;
        $this->second_approver_id ??= $levelTwo?->id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $leave) {
            $authUser = Auth::user();

            $leave->creator_id ??= $authUser?->id;

            $leave->company_id ??= current_company_id();

            $leave->populateApproversFromHierarchy();
        });
    }
}
