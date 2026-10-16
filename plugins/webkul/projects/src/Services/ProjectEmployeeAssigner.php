<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;

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

            if (! method_exists($project, 'addFollower') || ! Schema::hasTable('chatter_followers')) {
                return;
            }

            $assignedIds = collect(array_keys($syncPayload));
            $removed = $previousEmployees->reject(fn (Employee $employee): bool => $assignedIds->contains($employee->id));

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

        return [
            'assigned'     => count($syncPayload),
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
