<?php

namespace Webkul\Project\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Project\Services\OperationalScope;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Contract values are financial data: only Admin and the
        // budget-control team (Accounting / Accounting Manager)
        // receive them via the API as well.
        $canViewProjectValue = app(OperationalScope::class)->canViewProjectValue();

        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'tasks_label'             => $this->tasks_label,
            'description'             => $this->description,
            'visibility'              => $this->visibility,
            'color'                   => $this->color,
            'sort'                    => $this->sort,
            'start_date'              => $this->start_date,
            'end_date'                => $this->end_date,
            'project_value'           => $this->when($canViewProjectValue, fn (): float => (float) ($this->project_value ?? 0)),
            'operational_cost'        => (float) ($this->operational_cost ?? 0),
            'allocated_hours'         => (float) $this->allocated_hours,
            'allow_timesheets'        => (bool) $this->allow_timesheets,
            'allow_milestones'        => (bool) $this->allow_milestones,
            'allow_task_dependencies' => (bool) $this->allow_task_dependencies,
            'is_active'               => (bool) $this->is_active,
            'stage_id'                => $this->stage_id,
            'partner_id'              => $this->partner_id,
            'company_id'              => $this->company_id,
            'user_id'                 => $this->user_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'deleted_at'              => $this->deleted_at,
            'stage'                   => new ProjectStageResource($this->whenLoaded('stage')),
            'partner'                 => new PartnerResource($this->whenLoaded('partner')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'user'                    => new UserResource($this->whenLoaded('user')),
            'creator'                 => new UserResource($this->whenLoaded('creator')),
            'tasks'                   => TaskResource::collection($this->whenLoaded('tasks')),
            'taskStages'              => TaskStageResource::collection($this->whenLoaded('taskStages')),
            'milestones'              => MilestoneResource::collection($this->whenLoaded('milestones')),
            'tags'                    => TagResource::collection($this->whenLoaded('tags')),
            'favoriteUsers'           => UserResource::collection($this->whenLoaded('favoriteUsers')),
        ];
    }
}
