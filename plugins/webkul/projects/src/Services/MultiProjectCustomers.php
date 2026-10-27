<?php

namespace Webkul\Project\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

class MultiProjectCustomers
{
    public function partnerQuery(?User $user = null): Builder
    {
        $constrainProjects = $this->projectConstraint($user);

        return Partner::query()
            ->whereHas('projects', $constrainProjects, '>=', 2)
            ->withCount(['projects as customer_projects_count' => $constrainProjects])
            ->withSum(['projects as customer_projects_total_value' => $constrainProjects], 'project_value')
            ->with(['projects' => $constrainProjects]);
    }

    public function count(?User $user = null): int
    {
        return (int) $this->partnerQuery($user)->count();
    }

    /**
     * @return Closure(Builder): void
     */
    protected function projectConstraint(?User $user): Closure
    {
        return function ($query) use ($user): void {
            $query->where('is_active', true)->whereNotNull('partner_id');

            $projectIds = app(OperationalScope::class)->projectListScopeIds($user);

            if ($projectIds === []) {
                $query->whereRaw('1 = 0');

                return;
            }

            if ($projectIds !== null) {
                $query->whereIn($query->getModel()->getTable().'.id', $projectIds);
            }
        };
    }
}
