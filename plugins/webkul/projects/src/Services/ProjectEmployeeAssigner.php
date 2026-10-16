<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Services\SecurityAuditLogger;

class ProjectEmployeeAssigner
{
    /**
     * Sync employees assigned to a project and mirror access via Chatter followers.
     *
     * @param  array<int|string>  $employeeIds
     * @return array{assigned: int, without_user: int}
     */
    public function sync(Project $project, array $employeeIds, ?int $creatorId = null): array
    {
        $creatorId ??= Auth::id();
        $employeeIds = collect($employeeIds)
            ->filter(fn (mixed $id): bool => filled($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $employees = Employee::query()
            ->with(['user', 'partner'])
            ->whereIn('id', $employeeIds->all())
            ->get()
            ->keyBy('id');

        $previousEmployees = $project->employees()
            ->with(['user', 'partner'])
            ->get()
            ->keyBy('id');

        $syncPayload = [];
        $withoutUser = 0;
        $now = now();

        foreach ($employeeIds as $employeeId) {
            $employee = $employees->get($employeeId);

            if (! $employee instanceof Employee) {
                continue;
            }

            if (! $employee->user_id) {
                $withoutUser++;
            }

            $syncPayload[$employeeId] = [
                'user_id'    => $employee->user_id,
                'creator_id' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($project, $syncPayload, $previousEmployees, $employees): void {
            $project->employees()->sync($syncPayload);

            $assignedIds = collect(array_keys($syncPayload));
            $removed = $previousEmployees->reject(fn (Employee $employee): bool => $assignedIds->contains($employee->id));

            $this->detachRemovedUsersFromOpenTasks($project, $removed);

            if (! method_exists($project, 'addFollower') || ! Schema::hasTable('chatter_followers')) {
                return;
            }

            foreach ($removed as $employee) {
                $partner = $this->resolvePartner($employee);

                if ($partner instanceof Partner) {
                    $project->removeFollower($partner);
                }
            }

            foreach ($assignedIds as $employeeId) {
                $employee = $employees->get($employeeId) ?? $previousEmployees->get($employeeId);

                if (! $employee instanceof Employee) {
                    continue;
                }

                $partner = $this->resolvePartner($employee);

                if ($partner instanceof Partner) {
                    $project->addFollower($partner);
                }
            }
        });

        try {
            app(TeamMembershipSynchronizer::class)->syncForProject($project->fresh() ?? $project);
        } catch (\Throwable) {
        }

        $result = [
            'assigned'     => count($syncPayload),
            'without_user' => $withoutUser,
        ];

        try {
            app(SecurityAuditLogger::class)->log(
                SecurityAuditEvent::EmployeesAssigned,
                __('security::backup.audit.employees-assigned', [
                    'project' => $project->name,
                    'count'   => $result['assigned'],
                    'without' => $result['without_user'],
                ]),
                subject: $project,
                properties: [
                    'project_id'   => $project->id,
                    'employee_ids' => array_keys($syncPayload),
                    'assigned'     => $result['assigned'],
                    'without_user' => $result['without_user'],
                ],
            );
        } catch (\Throwable) {
        }

        return $result;
    }

    /**
     * Sync projects assigned to an employee and mirror access via Chatter followers.
     *
     * @param  array<int|string>  $projectIds
     * @return array{assigned: int, without_user: int}
     */
    public function syncProjectsForEmployee(Employee $employee, array $projectIds, ?int $creatorId = null): array
    {
        $creatorId ??= Auth::id();
        $projectIds = collect($projectIds)
            ->filter(fn (mixed $id): bool => filled($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $previousProjectIds = $employee->projects()->pluck('projects_projects.id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $desiredProjectIds = $projectIds->all();
        $toAttach = array_values(array_diff($desiredProjectIds, $previousProjectIds));
        $toDetach = array_values(array_diff($previousProjectIds, $desiredProjectIds));

        $withoutUser = $employee->user_id ? 0 : 1;

        DB::transaction(function () use ($employee, $toAttach, $toDetach, $creatorId): void {
            if ($toDetach !== []) {
                $employee->projects()->detach($toDetach);

                if (Schema::hasTable('chatter_followers')) {
                    $partner = $this->resolvePartner($employee->loadMissing(['user', 'partner']));

                    if ($partner instanceof Partner) {
                        Project::query()
                            ->whereIn('id', $toDetach)
                            ->get()
                            ->each(function (Project $project) use ($partner): void {
                                if (method_exists($project, 'removeFollower')) {
                                    $project->removeFollower($partner);
                                }
                            });
                    }
                }
            }

            if ($toAttach !== []) {
                $now = now();
                $payload = [];

                foreach ($toAttach as $projectId) {
                    $payload[$projectId] = [
                        'user_id'    => $employee->user_id,
                        'creator_id' => $creatorId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $employee->projects()->attach($payload);

                $partner = $this->resolvePartner($employee->loadMissing(['user', 'partner']));

                if ($partner instanceof Partner && Schema::hasTable('chatter_followers')) {
                    Project::query()
                        ->whereIn('id', $toAttach)
                        ->get()
                        ->each(function (Project $project) use ($partner): void {
                            if (method_exists($project, 'addFollower')) {
                                $project->addFollower($partner);
                            }
                        });
                }
            }
        });

        $projectIdsToSync = array_values(array_unique(array_merge($desiredProjectIds, $toDetach)));

        if ($projectIdsToSync !== []) {
            try {
                Project::query()
                    ->whereIn('id', $projectIdsToSync)
                    ->get()
                    ->each(fn (Project $project) => app(TeamMembershipSynchronizer::class)->syncForProject($project));
            } catch (\Throwable) {
            }
        }

        return [
            'assigned'     => count($desiredProjectIds),
            'without_user' => $withoutUser,
        ];
    }

    /**
     * @return Collection<int, Employee>
     */
    public function assignedEmployees(Project $project): Collection
    {
        return $project->employees()
            ->with(['user', 'department'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Project>
     */
    public function assignedProjects(Employee $employee): Collection
    {
        return $employee->projects()
            ->with(['partner', 'user'])
            ->orderBy('name')
            ->get();
    }

    /**
     * When employees leave a project, detach their users from open (non-done) tasks.
     *
     * @param  Collection<int, Employee>  $removed
     */
    protected function detachRemovedUsersFromOpenTasks(Project $project, Collection $removed): void
    {
        if ($removed->isEmpty() || ! Schema::hasTable('projects_tasks') || ! Schema::hasTable('projects_task_users')) {
            return;
        }

        $userIds = $removed->pluck('user_id')->filter()->map(fn ($id): int => (int) $id)->unique()->values()->all();

        if ($userIds === []) {
            return;
        }

        $taskIds = Task::query()
            ->where('project_id', $project->id)
            ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value])
            ->pluck('id')
            ->all();

        if ($taskIds === []) {
            return;
        }

        DB::table('projects_task_users')
            ->whereIn('task_id', $taskIds)
            ->whereIn('user_id', $userIds)
            ->delete();
    }

    /**
     * Task assignees on a project whose user is not linked to an assigned employee.
     *
     * @return Collection<int, array{task_id: int, task: string, user_id: int, user: string}>
     */
    public function assigneeInconsistencies(Project $project): Collection
    {
        if (! Schema::hasTable('projects_task_users') || ! Schema::hasTable('projects_tasks')) {
            return collect();
        }

        $assignedUserIds = $project->employees()
            ->whereNotNull('employees_employees.user_id')
            ->pluck('employees_employees.user_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($project->user_id) {
            $assignedUserIds[] = (int) $project->user_id;
        }

        $assignedUserIds = array_values(array_unique($assignedUserIds));

        return Task::query()
            ->with(['users:id,name'])
            ->where('project_id', $project->id)
            ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value])
            ->get()
            ->flatMap(function (Task $task) use ($assignedUserIds): Collection {
                return $task->users
                    ->reject(fn ($user): bool => in_array((int) $user->id, $assignedUserIds, true))
                    ->map(fn ($user): array => [
                        'task_id' => $task->id,
                        'task'    => (string) $task->title,
                        'user_id' => (int) $user->id,
                        'user'    => (string) $user->name,
                    ]);
            })
            ->values();
    }

    protected function resolvePartner(Employee $employee): ?Partner
    {
        if ($employee->partner instanceof Partner) {
            return $employee->partner;
        }

        if ($employee->user?->partner_id) {
            return Partner::query()->find($employee->user->partner_id);
        }

        return null;
    }
}
