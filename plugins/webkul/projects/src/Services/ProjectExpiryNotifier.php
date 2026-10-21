<?php

namespace Webkul\Project\Services;

use Filament\Actions\Action;
use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

class ProjectExpiryNotifier
{
    public const ALERT_WINDOW_DAYS = 90;

    /**
     * @return Collection<int, Project>
     */
    public function expiringProjects(int $withinDays = self::ALERT_WINDOW_DAYS): Collection
    {
        if (! Schema::hasTable('projects_projects') || ! Schema::hasColumn('projects_projects', 'end_date')) {
            return collect();
        }

        $cutoffDate = $withinDays >= self::ALERT_WINDOW_DAYS
            ? max(now()->addDays($withinDays)->toDateString(), now()->addMonths(3)->toDateString())
            : now()->addDays($withinDays)->toDateString();

        return Project::withoutGlobalScopes()
            ->with(['partner', 'user'])
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $cutoffDate)
            ->orderBy('end_date')
            ->get();
    }

    public function notifyExpiringProjects(int $withinDays = self::ALERT_WINDOW_DAYS): int
    {
        $projects = $this->expiringProjects($withinDays);

        if ($projects->isEmpty()) {
            return 0;
        }

        $sentCount = 0;

        foreach ($projects as $project) {
            $sentCount += $this->notifyForProject($project, $withinDays);
        }

        return $sentCount;
    }

    public function notifyForProject(Project $project, int $withinDays = self::ALERT_WINDOW_DAYS): int
    {
        if (! $project->is_active || $project->trashed() || ! $project->end_date) {
            return 0;
        }

        $days = $project->daysUntilEndDate();
        $stage = $project->expiryReminderStage();

        if ($days === null || $stage === null) {
            return 0;
        }

        if ($withinDays < self::ALERT_WINDOW_DAYS && $days > $withinDays) {
            return 0;
        }

        $endDateString = $project->end_date->toDateString();
        $formattedDate = $project->end_date->format('d M Y');
        $customerName = $project->partner?->name ?? '—';
        $formattedValue = $project->formattedProjectValue();

        $stageLabel = match ($stage) {
            '1_month'  => __('projects::filament/resources/project.notifications.stages.1_month'),
            '2_months' => __('projects::filament/resources/project.notifications.stages.2_months'),
            '3_months' => __('projects::filament/resources/project.notifications.stages.3_months'),
            default    => __('projects::filament/resources/project.notifications.stages.expired'),
        };

        $title = $stage === 'expired'
            ? __('projects::filament/resources/project.notifications.project-expired.title', [
                'name' => $project->name,
            ])
            : __('projects::filament/resources/project.notifications.project-expiring.title', [
                'stage' => $stageLabel,
                'name'  => $project->name,
            ]);

        $url = $this->projectViewUrl($project);

        $color = match ($stage) {
            'expired', '1_month' => 'danger',
            '2_months'           => 'warning',
            default              => 'info',
        };

        $sentCount = 0;

        foreach ($this->recipientsFor($project) as $recipient) {
            if ($this->alreadyNotified($recipient, $project->id, $endDateString, $stage)) {
                continue;
            }

            // Contract values are financial data: recipients outside
            // Admin / budget-control get the value-free body variant.
            $withValue = app(OperationalScope::class)->canViewProjectValue($recipient);

            $body = $stage === 'expired'
                ? __('projects::filament/resources/project.notifications.project-expired.'.($withValue ? 'body' : 'body-no-value'), [
                    'name'     => $project->name,
                    'customer' => $customerName,
                    'value'    => $formattedValue,
                    'date'     => $formattedDate,
                ])
                : __('projects::filament/resources/project.notifications.project-expiring.'.($withValue ? 'body' : 'body-no-value'), [
                    'stage'    => $stageLabel,
                    'name'     => $project->name,
                    'customer' => $customerName,
                    'value'    => $formattedValue,
                    'days'     => $days,
                    'date'     => $formattedDate,
                ]);

            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($color);

            if ($url !== null) {
                $notification->actions([
                    Action::make('view')
                        ->label(__('projects::filament/resources/project.notifications.view-project'))
                        ->url($url)
                        ->markAsRead(),
                ]);
            }

            $message = $notification->getDatabaseMessage();
            $message['project_expiry_project_id'] = $project->id;
            $message['project_expiry_end_date'] = $endDateString;
            $message['project_expiry_stage'] = $stage;

            $recipient->notifications()->create([
                'id'   => (string) str()->uuid(),
                'type' => FilamentDatabaseNotification::class,
                'data' => $message,
            ]);

            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * @return Collection<int, User>
     */
    protected function recipientsFor(Project $project): Collection
    {
        $users = User::query()
            ->get()
            ->filter(fn (User $user): bool => $user->can('view_any_project_project') || $user->can('update_project_project'));

        if ($project->user_id) {
            $managerUser = User::query()->find($project->user_id);

            if ($managerUser) {
                $users->push($managerUser);
            }
        }

        if ($project->creator_id) {
            $creatorUser = User::query()->find($project->creator_id);

            if ($creatorUser) {
                $users->push($creatorUser);
            }
        }

        return $users->unique('id')->values();
    }

    protected function alreadyNotified(User $user, int $projectId, string $endDate, string $stage): bool
    {
        return $user->notifications()
            ->where('type', FilamentDatabaseNotification::class)
            ->get()
            ->contains(function ($notification) use ($projectId, $endDate, $stage): bool {
                $data = (array) $notification->data;

                return ($data['project_expiry_project_id'] ?? null) === $projectId
                    && ($data['project_expiry_end_date'] ?? null) === $endDate
                    && ($data['project_expiry_stage'] ?? null) === $stage;
            });
    }

    protected function projectViewUrl(Project $project): ?string
    {
        try {
            return ProjectResource::getUrl('view', ['record' => $project]);
        } catch (\Throwable) {
            return url('/admin/project/projects/'.$project->id);
        }
    }
}
