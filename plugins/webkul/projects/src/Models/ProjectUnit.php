<?php

namespace Webkul\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Services\ProjectGovernance;

class ProjectUnit extends Model
{
    protected $table = 'projects_project_units';

    protected $fillable = [
        'project_id',
        'department_id',
        'mos_employee_id',
        'team_leader_employee_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function mos(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'mos_employee_id');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'team_leader_employee_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $unit): void {
            ProjectGovernance::assertUnitLeadership(
                (int) $unit->mos_employee_id,
                (int) $unit->team_leader_employee_id,
            );
            ProjectGovernance::assertUnitUniqueness($unit);
        });

        static::saved(function (self $unit): void {
            if (! Schema::hasTable('projects_project_employees') || ! $unit->project) {
                return;
            }

            $memberIds = array_values(array_filter([
                (int) $unit->mos_employee_id,
                (int) $unit->team_leader_employee_id,
            ]));

            if ($memberIds !== []) {
                $unit->project->employees()->syncWithoutDetaching($memberIds);
            }
        });
    }
}
