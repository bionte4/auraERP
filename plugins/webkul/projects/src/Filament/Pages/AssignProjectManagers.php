<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectManagerBulkImportService;
use Webkul\Project\Services\TeamMembershipSynchronizer;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\NavigationBadge;

class AssignProjectManagers extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'project/assign-project-managers';

    protected string $view = 'projects::filament.pages.assign-project-managers';

    public string $search = '';

    public string $managerFilter = 'all';

    protected static function getPagePermission(): ?string
    {
        return 'page_project_assign_project_managers';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/assign-project-managers.navigation.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon;
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/assign-project-managers.title');
    }

    public static function getNavigationBadge(): ?string
    {
        return NavigationBadge::count('projects-without-manager', fn (): int => Schema::hasTable('projects_projects')
            ? Project::query()->whereNull('user_id')->count()
            : 0);
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('projects::filament/pages/assign-project-managers.navigation.badge-tooltip');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function setManagerFilter(string $value): void
    {
        $this->managerFilter = $value;
    }

    protected function getHeaderActions(): array
    {
        $lang = 'projects::filament/pages/assign-project-managers';

        return [
            Action::make('downloadTemplate')
                ->label(__($lang.'.actions.template'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(ProjectManagerBulkImportService::class)->downloadTemplate()),

            Action::make('bulkImport')
                ->label(__($lang.'.actions.import'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__($lang.'.actions.import-heading'))
                ->modalDescription(__($lang.'.actions.import-description'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__($lang.'.actions.import-file'))
                        ->disk('local')
                        ->directory('imports/project-managers')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) use ($lang): void {
                    $relativePath = (string) ($data['file'] ?? '');
                    $fullPath = Storage::disk('local')->path($relativePath);

                    $result = app(ProjectManagerBulkImportService::class)->importFromPath($fullPath);

                    Storage::disk('local')->delete($relativePath);

                    $body = __($lang.'.notifications.imported.body', $result);

                    if ($result['errors'] !== []) {
                        $body .= ' '.implode(' ', array_slice($result['errors'], 0, 8));
                    }

                    Notification::make()
                        ->success()
                        ->title(__($lang.'.notifications.imported.title'))
                        ->body($body)
                        ->persistent()
                        ->send();
                }),
        ];
    }

    /**
     * Operation Manager users with their managed-project counts,
     * used for the summary chips and the row dropdowns.
     *
     * @return array<int, array{id: int, name: string, email: ?string, projects: int}>
     */
    public function getManagerSummaries(): array
    {
        $managers = User::query()
            ->whereHas('roles', fn ($query) => $query
                ->where('name', 'Operation Manager')
                ->where('guard_name', 'web'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $counts = Project::query()
            ->whereIn('user_id', $managers->pluck('id')->all())
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return $managers->map(fn (User $user): array => [
            'id'       => (int) $user->id,
            'name'     => (string) $user->name,
            'email'    => $user->email,
            'projects' => (int) ($counts[$user->id] ?? 0),
        ])->all();
    }

    public function getUnassignedCount(): int
    {
        return Project::query()->whereNull('user_id')->count();
    }

    /**
     * Project rows for the bulk grid: unassigned projects first,
     * then alphabetical. Scoped by the search box and manager filter.
     *
     * @return array<int, array{id: int, name: string, customer: string, members: int, end_date: string, user_id: ?int}>
     */
    public function getProjectRows(): array
    {
        $memberCounts = Schema::hasTable('projects_project_employees')
            ? DB::table('projects_project_employees')
                ->selectRaw('project_id, count(*) as total')
                ->groupBy('project_id')
                ->pluck('total', 'project_id')
            : collect();

        $projects = Project::query()
            ->with([
                'partner' => fn ($query) => $query->select('id', 'name'),
            ])
            ->when(filled($this->search), fn ($query) => $query->where('name', 'ilike', '%'.trim($this->search).'%'))
            ->when($this->managerFilter === 'unassigned', fn ($query) => $query->whereNull('user_id'))
            ->when(str_starts_with($this->managerFilter, 'user:'), fn ($query) => $query->where('user_id', (int) substr($this->managerFilter, 5)))
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->get();

        return $projects->map(fn (Project $project): array => [
            'id'       => (int) $project->id,
            'name'     => (string) $project->name,
            'customer' => (string) ($project->partner?->name ?? '—'),
            'members'  => (int) ($memberCounts[$project->id] ?? 0),
            'end_date' => $project->end_date
                ? Carbon::parse($project->end_date)->format('d M Y')
                : '—',
            'user_id' => $project->user_id ? (int) $project->user_id : null,
        ])->all();
    }

    /**
     * Assign (or clear) the Project Manager of one project. The new
     * manager is synced into the project team right away.
     */
    public function assignManager(int $projectId, mixed $userId): void
    {
        $project = Project::query()->findOrFail($projectId);
        $userId = blank($userId) ? null : (int) $userId;

        if ($userId !== null) {
            $manager = User::query()->find($userId);

            $isManager = $manager && $manager->roles()
                ->where('name', 'Operation Manager')
                ->where('guard_name', 'web')
                ->exists();

            if (! $isManager) {
                Notification::make()
                    ->danger()
                    ->title(__('projects::filament/pages/assign-project-managers.notifications.invalid-manager.title'))
                    ->send();

                return;
            }
        }

        if ((int) ($project->user_id ?? 0) === (int) ($userId ?? 0)) {
            return;
        }

        $project->forceFill(['user_id' => $userId])->save();

        if (class_exists(TeamMembershipSynchronizer::class)) {
            try {
                app(TeamMembershipSynchronizer::class)->syncForProject($project);
            } catch (\Throwable) {
                // Team sync is best-effort; the assignment itself stands.
            }
        }

        Notification::make()
            ->success()
            ->title(__('projects::filament/pages/assign-project-managers.notifications.saved.title'))
            ->body($userId === null
                ? __('projects::filament/pages/assign-project-managers.notifications.saved.body-cleared', ['project' => $project->name])
                : __('projects::filament/pages/assign-project-managers.notifications.saved.body', [
                    'project' => $project->name,
                    'manager' => User::query()->whereKey($userId)->value('name'),
                ]))
            ->send();
    }
}
