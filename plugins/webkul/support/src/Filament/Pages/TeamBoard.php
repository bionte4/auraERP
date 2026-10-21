<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Project\Services\TeamMembershipSynchronizer;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

class TeamBoard extends Page
{
    protected string $view = 'filament.pages.team-board';

    protected static ?string $slug = 'team-board';

    protected static ?int $navigationSort = -8;

    public string $viewMode = 'cards';

    public string $search = '';

    public string $sortBy = 'name_asc';

    public ?int $editingTeamId = null;

    public ?int $addEmployeeId = null;

    /**
     * Sort options offered on the board (cards, compact, and list modes).
     *
     * @var array<int, string>
     */
    protected static array $sortOptions = [
        'name_asc',
        'name_desc',
        'members_desc',
        'projects_desc',
        'tasks_desc',
        'progress_desc',
        'progress_asc',
        'value_desc',
        'updated_desc',
    ];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole')) {
            return false;
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        return $user->hasRole($adminRole)
            || $user->hasRole('Operation Manager')
            || $user->hasRole('Team Leader')
            || $user->hasRole('Staff');
    }

    public static function getNavigationLabel(): string
    {
        return static::managesTeams() ? 'Team Board' : 'Tim Saya';
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-group';
    }

    public function mount(): void
    {
        $saved = (string) session('team-board.view-mode', 'cards');

        $this->viewMode = in_array($saved, ['cards', 'compact', 'list', 'kanban'], true) ? $saved : 'cards';

        $savedSort = (string) session('team-board.sort', 'name_asc');

        $this->sortBy = in_array($savedSort, static::$sortOptions, true) ? $savedSort : 'name_asc';
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['cards', 'compact', 'list', 'kanban'], true) ? $mode : 'cards';

        session(['team-board.view-mode' => $this->viewMode]);
    }

    public function setSortBy(string $sort): void
    {
        $this->sortBy = in_array($sort, static::$sortOptions, true) ? $sort : 'name_asc';

        session(['team-board.sort' => $this->sortBy]);
    }

    public function moveEmployeeOnKanban(int $employeeId, int $fromProjectId, int $toProjectId): void
    {
        if (! static::managesTeams() || $fromProjectId === $toProjectId) {
            return;
        }

        $from = Project::query()->find($fromProjectId);
        $to = Project::query()->find($toProjectId);
        $employee = class_exists(Employee::class) ? Employee::query()->find($employeeId) : null;

        if (! $from || ! $to || ! $employee) {
            return;
        }

        $assigner = app(ProjectEmployeeAssigner::class);
        $fromIds = $assigner->assignedEmployees($from)->pluck('id')->reject(fn ($id): bool => (int) $id === $employeeId)->values()->all();
        $toIds = $assigner->assignedEmployees($to)->pluck('id')->push($employeeId)->unique()->values()->all();

        $assigner->sync($from, $fromIds);
        $assigner->sync($to, $toIds);

        Notification::make()
            ->success()
            ->title(str_starts_with((string) app()->getLocale(), 'id')
                ? 'Penugasan dipindahkan'
                : 'Assignment moved')
            ->body($employee->name.' → '.$to->name)
            ->send();
    }

    public function startEditingMembers(int $teamId): void
    {
        if (! static::managesTeams()) {
            return;
        }

        $this->editingTeamId = $teamId;
        $this->addEmployeeId = null;
    }

    public function stopEditingMembers(): void
    {
        $this->editingTeamId = null;
        $this->addEmployeeId = null;
    }

    public function addEmployeeToTeam(): void
    {
        if (! static::managesTeams() || ! $this->editingTeamId || ! $this->addEmployeeId) {
            return;
        }

        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $cards = $this->cardsFor(auth()->user());
        $card = collect($cards)->firstWhere('id', $this->editingTeamId);

        if ($card === null || ($card['primary_project_id'] ?? null) === null) {
            Notification::make()
                ->warning()
                ->title($indonesian ? 'Tim belum tertaut proyek' : 'Team is not linked to a project')
                ->body($indonesian
                    ? 'Tautkan tim ke proyek atau samakan nama tim dengan nama proyek sebelum menambah karyawan.'
                    : 'Link the team to a project or match the team name to a project name before adding employees.')
                ->send();

            return;
        }

        $project = Project::query()->withoutGlobalScopes()->find($card['primary_project_id']);
        $employee = Employee::query()->find($this->addEmployeeId);

        if (! $project instanceof Project || ! $employee instanceof Employee) {
            return;
        }

        $assigner = app(ProjectEmployeeAssigner::class);
        $ids = $assigner->assignedEmployees($project)->pluck('id')->push($employee->id)->unique()->values()->all();
        $assigner->sync($project, $ids);

        $this->addEmployeeId = null;

        Notification::make()
            ->success()
            ->title($indonesian ? 'Karyawan ditambahkan' : 'Employee added')
            ->body($employee->name)
            ->send();
    }

    public function removeEmployeeFromTeam(int $teamId, int $employeeId): void
    {
        if (! static::managesTeams()) {
            return;
        }

        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $cards = $this->cardsFor(auth()->user());
        $card = collect($cards)->firstWhere('id', $teamId);

        if ($card === null) {
            return;
        }

        $employee = Employee::query()->find($employeeId);

        if (! $employee instanceof Employee) {
            return;
        }

        $assigner = app(ProjectEmployeeAssigner::class);
        $removedFrom = [];

        foreach ($card['projects'] ?? [] as $projectLink) {
            $projectId = (int) ($projectLink['id'] ?? 0);
            $project = $projectId > 0 ? Project::query()->withoutGlobalScopes()->find($projectId) : null;

            if (! $project instanceof Project) {
                continue;
            }

            $currentIds = $assigner->assignedEmployees($project)->pluck('id')->map(fn ($id): int => (int) $id);
            $nextIds = $currentIds->reject(fn (int $id): bool => $id === $employeeId)->values()->all();

            if (count($nextIds) === $currentIds->count()) {
                continue;
            }

            $assigner->sync($project, $nextIds);
            $removedFrom[] = $project->name;
        }

        if ($removedFrom === []) {
            Notification::make()
                ->warning()
                ->title($indonesian ? 'Anggota tidak dapat dihapus' : 'Member cannot be removed')
                ->body($indonesian
                    ? 'Manajer Proyek tanpa penugasan karyawan tidak dihapus dari roster di sini. Ubah Manajer Proyek di form proyek.'
                    : 'A Project Manager who is not an assigned employee is not removed here. Change the Project Manager on the project form.')
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title($indonesian ? 'Karyawan dilepas dari proyek' : 'Employee unassigned from project')
            ->body($employee->name.' — '.implode(', ', $removedFrom))
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    public function getBoardData(): array
    {
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $manages = static::managesTeams();
        $rawCards = $this->cardsFor(auth()->user());
        $cards = $this->sortCards($this->filterCards($rawCards));

        $canViewValue = false;
        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (class_exists($scopeClass)) {
            $canViewValue = app($scopeClass)->canViewProjectValue();
        }

        return [
            'manages'      => $manages,
            'viewMode'     => $this->viewMode,
            'sortBy'       => $this->sortBy,
            'canViewValue' => $canViewValue,
            'kanban'       => $this->viewMode === 'kanban' ? $this->kanbanColumns(auth()->user()) : [],
            'title'        => $manages
                ? ($indonesian ? 'Kelola Tim' : 'Manage Teams')
                : ($indonesian ? 'Tim Saya' : 'My Team'),
            'subtitle' => $manages
                ? ($indonesian
                    ? 'Pantau anggota, proyek berjalan, dan kemajuan tim yang Anda kelola. Mode Kanban: seret karyawan antar proyek.'
                    : 'Track members, active projects, and progress for the teams you manage. Kanban: drag employees between projects.')
                : ($indonesian
                    ? 'Proyek dan kemajuan tim tempat Anda menjadi anggota.'
                    : 'Projects and progress for the team you belong to.'),
            'action'    => $this->headerAction($indonesian),
            'cards'     => $cards,
            'searching' => filled($this->search),
            'empty'     => $manages
                ? ($indonesian
                    ? 'Belum ada tim. Buat tim, atau pastikan nama Tim sama dengan nama Proyek agar anggota tersinkron.'
                    : 'No team yet. Create a team, or match the team name to a project name so members sync.')
                : ($indonesian
                    ? 'Anda belum masuk ke sebuah tim. Minta Operation Manager menambahkan Anda.'
                    : 'You are not on a team yet. Ask an Operation Manager to add you.'),
            'labels'   => [
                'members'            => $indonesian ? 'anggota' : 'members',
                'members-click'      => $indonesian ? 'Klik untuk melihat daftar karyawan' : 'Click to view employees',
                'members-list'       => $indonesian ? 'Karyawan terlibat' : 'Involved employees',
                'role-manager'       => $indonesian ? 'Manajer Proyek' : 'Project Manager',
                'role-assigned'      => $indonesian ? 'Ditugaskan ke proyek' : 'Assigned to project',
                'role-team'          => $indonesian ? 'Anggota tim' : 'Team member',
                'about'              => $indonesian ? 'Tentang tim' : 'About team',
                'projects'           => $indonesian ? 'Proyek' : 'Projects',
                'tasks'              => $indonesian ? 'Tugas' : 'Tasks',
                'value'              => $indonesian ? 'Nilai' : 'Value',
                'status'             => $indonesian ? 'Status proyek berjalan' : 'Status of current projects',
                'updated'            => $indonesian ? 'Diperbarui' : 'Updated',
                'details'            => $indonesian ? 'Detail' : 'Details',
                'new'                => $indonesian ? 'Baru' : 'New',
                'open'               => $indonesian ? 'Buka' : 'Open',
                'mode-cards'         => $indonesian ? 'Kartu Tim' : 'Team cards',
                'mode-compact'       => $indonesian ? 'Ringkas' : 'Compact',
                'mode-list'          => $indonesian ? 'Daftar' : 'List',
                'mode-kanban'        => $indonesian ? 'Kanban Proyek' : 'Project kanban',
                'drop-here'          => $indonesian ? 'Lepas di sini' : 'Drop here',
                'team'               => $indonesian ? 'Tim' : 'Team',
                'search-placeholder' => $indonesian ? 'Cari tim atau proyek…' : 'Search teams or projects…',
                'no-results'         => $indonesian ? 'Tidak ada tim yang cocok dengan pencarian.' : 'No teams match your search.',
                'sort'               => $indonesian ? 'Urutkan' : 'Sort',
                'sort-name_asc'      => $indonesian ? 'Nama A–Z' : 'Name A–Z',
                'sort-name_desc'     => $indonesian ? 'Nama Z–A' : 'Name Z–A',
                'sort-members_desc'  => $indonesian ? 'Anggota terbanyak' : 'Most members',
                'sort-projects_desc' => $indonesian ? 'Proyek terbanyak' : 'Most projects',
                'sort-tasks_desc'    => $indonesian ? 'Task terbanyak' : 'Most tasks',
                'sort-progress_desc' => $indonesian ? 'Progress tertinggi' : 'Highest progress',
                'sort-progress_asc'  => $indonesian ? 'Progress terendah' : 'Lowest progress',
                'sort-value_desc'    => $indonesian ? 'Nilai terbesar' : 'Highest value',
                'sort-updated_desc'  => $indonesian ? 'Terakhir diperbarui' : 'Recently updated',
                'no-login'           => $indonesian ? 'Tanpa login' : 'No login',
                'edit-members'       => $indonesian ? 'Ubah anggota' : 'Edit members',
                'done-editing'       => $indonesian ? 'Selesai' : 'Done',
                'add-member'         => $indonesian ? 'Tambah karyawan' : 'Add employee',
                'add'                => $indonesian ? 'Tambah' : 'Add',
                'remove-member'      => $indonesian ? 'Lepas dari proyek' : 'Unassign from project',
                'choose-employee'    => $indonesian ? 'Pilih karyawan…' : 'Choose an employee…',
            ],
            'candidateEmployees' => $this->candidateEmployeesForEditor($rawCards),
            'editingTeamId'      => $this->editingTeamId,
        ];
    }

    /**
     * Filter team cards by the board search box (team name or any of
     * its project names).
     *
     * @param  array<int, array<string, mixed>>  $cards
     * @return array<int, array<string, mixed>>
     */
    protected function filterCards(array $cards): array
    {
        $needle = mb_strtolower(trim($this->search));

        if ($needle === '') {
            return $cards;
        }

        return array_values(array_filter($cards, function (array $card) use ($needle): bool {
            if (str_contains(mb_strtolower((string) ($card['name'] ?? '')), $needle)) {
                return true;
            }

            foreach ($card['projects'] ?? [] as $project) {
                if (str_contains(mb_strtolower((string) ($project['name'] ?? '')), $needle)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * Order team cards per the board sort dropdown. Unknown sort keys
     * fall back to name A–Z. Teams the viewer may not price (value
     * hidden) always sink to the bottom on the value sort.
     *
     * @param  array<int, array<string, mixed>>  $cards
     * @return array<int, array<string, mixed>>
     */
    protected function sortCards(array $cards): array
    {
        $sorted = match ($this->sortBy) {
            'name_desc'     => collect($cards)->sortByDesc(fn (array $card): string => mb_strtolower((string) ($card['name'] ?? ''))),
            'members_desc'  => collect($cards)->sortByDesc(fn (array $card): int => (int) ($card['member_count'] ?? 0)),
            'projects_desc' => collect($cards)->sortByDesc(fn (array $card): int => (int) ($card['project_count'] ?? 0)),
            'tasks_desc'    => collect($cards)->sortByDesc(fn (array $card): int => (int) ($card['task_count'] ?? 0)),
            'progress_desc' => collect($cards)->sortByDesc(fn (array $card): int => (int) ($card['progress'] ?? 0)),
            'progress_asc'  => collect($cards)->sortBy(fn (array $card): int => (int) ($card['progress'] ?? 0)),
            'value_desc'    => collect($cards)->sortByDesc(fn (array $card): float => (float) ($card['value_amount'] ?? -1)),
            'updated_desc'  => collect($cards)->sortByDesc(fn (array $card): int => (int) ($card['updated_ts'] ?? 0)),
            default         => collect($cards)->sortBy(fn (array $card): string => mb_strtolower((string) ($card['name'] ?? ''))),
        };

        return $sorted->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function kanbanColumns(?User $user): array
    {
        if (! $user || ! Schema::hasTable('projects_projects') || ! Schema::hasTable('projects_project_employees')) {
            return [];
        }

        $cards = $this->cardsFor($user);
        $projectIds = collect($cards)
            ->flatMap(fn (array $card) => collect($card['projects'] ?? [])->pluck('id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($projectIds === []) {
            return [];
        }

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->with(['employees' => fn ($q) => $q->select('employees_employees.id', 'employees_employees.name', 'employees_employees.user_id')])
            ->whereIn('id', $projectIds)
            ->orderBy('name')
            ->get();

        $palette = ['#6366f1', '#0ea5e9', '#14b8a6', '#f59e0b', '#ec4899', '#8b5cf6'];

        return $projects->map(function (Project $project, int $index) use ($palette): array {
            return [
                'id'        => $project->id,
                'name'      => $project->name,
                'href'      => $this->projectViewUrl($project),
                'employees' => $project->employees->map(function (Employee $employee, int $i) use ($palette): array {
                    return [
                        'id'        => $employee->id,
                        'name'      => $employee->name,
                        'initials'  => Str::upper(Str::substr($employee->name, 0, 1)),
                        'color'     => $palette[$i % count($palette)],
                        'href'      => $this->employeeViewUrl($employee),
                        'has_login' => filled($employee->user_id),
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }

    protected static function managesTeams(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole')) {
            return false;
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        return $user->hasRole($adminRole) || $user->hasRole('Operation Manager');
    }

    /**
     * @return array{label: string, href: string}|null
     */
    protected function headerAction(bool $indonesian): ?array
    {
        $user = auth()->user();

        if (! $user || ! static::managesTeams()) {
            return null;
        }

        if ($user->can('create_security_team')) {
            return [
                'label' => $indonesian ? 'Tambah Tim' : 'Add New Team',
                'href'  => url('/admin/teams'),
            ];
        }

        if ($user->can('create_project_project')) {
            return [
                'label' => $indonesian ? 'Proyek Baru' : 'New Project',
                'href'  => url('/admin/project/projects/create'),
            ];
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function cardsFor(?User $user): array
    {
        if (! $user || ! Schema::hasTable('teams') || ! Schema::hasTable('user_team')) {
            return [];
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');
        $allTeams = Team::query()
            ->withoutGlobalScopes()
            ->with(['users' => fn ($users) => $users->withoutGlobalScopes()->select('users.id', 'users.name')->orderBy('users.name')])
            ->orderBy('id')
            ->get();

        $teams = $user->hasRole($adminRole)
            ? $allTeams
            : $allTeams->filter(function (Team $team) use ($user): bool {
                return (int) $team->creator_id === (int) $user->id
                    || $team->users->contains('id', $user->id);
            })->values();

        if ($teams->isEmpty()) {
            return [];
        }

        if (class_exists(TeamMembershipSynchronizer::class)) {
            foreach ($teams as $team) {
                try {
                    app(TeamMembershipSynchronizer::class)->syncForTeam($team);
                    $team->load(['users' => fn ($users) => $users->withoutGlobalScopes()->select('users.id', 'users.name')->orderBy('users.name')]);
                } catch (Throwable) {
                }
            }
        }

        $projectsByTeam = $this->projectsGroupedByTeam($teams);

        // Scoped viewers (Operation Manager, Team Leader, Staff) only
        // see their own assigned projects on team cards — not every
        // project owned by fellow team members. Admin sees all.
        $allowedProjectIds = $this->assignedProjectIdsFor($user);

        if ($allowedProjectIds !== null) {
            $projectsByTeam = $projectsByTeam->map(
                fn (Collection $projects): Collection => $projects
                    ->filter(fn (Project $project): bool => in_array((int) $project->id, $allowedProjectIds, true))
                    ->values()
            );
        }

        $allProjectIds = $projectsByTeam->flatten(1)->pluck('id')->unique()->all();
        $taskStats = $this->taskStats($allProjectIds);

        return $teams->map(function (Team $team) use ($projectsByTeam, $taskStats): array {
            $teamProjects = $projectsByTeam->get($team->id, collect());

            return $this->presentCard($team, $teamProjects, $taskStats, str_starts_with((string) app()->getLocale(), 'id'));
        })->all();
    }

    /**
     * Project IDs the board viewer is restricted to, or null when
     * unrestricted (Admin). Delegates to the projects plugin's
     * OperationalScope so the board matches the Projects list scope.
     *
     * @return array<int, int>|null
     */
    protected function assignedProjectIdsFor(User $user): ?array
    {
        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        if (method_exists($user, 'hasRole') && $user->hasRole($adminRole)) {
            return null;
        }

        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! class_exists($scopeClass)) {
            return null;
        }

        $ids = app($scopeClass)->projectListScopeIds($user);

        return $ids === null ? null : array_map('intval', $ids);
    }

    /**
     * @param  Collection<int, Team>  $teams
     * @return Collection<int, Collection<int, Project>>
     */
    protected function projectsGroupedByTeam(Collection $teams): Collection
    {
        if (! Schema::hasTable('projects_projects') || $teams->isEmpty()) {
            return collect();
        }

        $names = $teams->map(fn (Team $team): string => mb_strtolower(trim($team->name)))->unique()->values();
        $memberIds = $teams->flatMap(fn (Team $team) => $team->users->pluck('id'))->unique()->values()->all();
        $linkedProjectIds = Schema::hasColumn('teams', 'project_id')
            ? $teams->pluck('project_id')->filter()->unique()->values()->all()
            : [];

        $projectsQuery = Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($names, $memberIds, $linkedProjectIds): void {
                if ($linkedProjectIds !== []) {
                    $query->whereIn('id', $linkedProjectIds);
                }

                $query->orWhere(function ($nameQuery) use ($names): void {
                    foreach ($names as $name) {
                        $nameQuery->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
                    }
                });

                if ($memberIds !== []) {
                    $query->orWhereIn('user_id', $memberIds);
                }
            })
            ->orderByDesc('updated_at');

        $with = [
            'user' => fn ($query) => $query->select('users.id', 'users.name'),
        ];

        if (class_exists(Employee::class) && Schema::hasTable('projects_project_employees')) {
            $with['employees'] = fn ($query) => $query->select(
                'employees_employees.id',
                'employees_employees.name',
                'employees_employees.user_id'
            );
        }

        $projects = $projectsQuery->with($with)->get();

        $teamsByName = $teams->keyBy(fn (Team $team): string => mb_strtolower(trim($team->name)));
        $teamsByProjectId = Schema::hasColumn('teams', 'project_id')
            ? $teams->filter(fn (Team $team): bool => filled($team->project_id))->keyBy('project_id')
            : collect();

        $grouped = collect();

        foreach ($teams as $team) {
            $grouped[$team->id] = collect();
        }

        foreach ($projects as $project) {
            $linked = $teamsByProjectId->get($project->id);

            if ($linked instanceof Team) {
                $grouped[$linked->id] = $grouped[$linked->id]->push($project)->unique('id')->values();

                continue;
            }

            $matched = $teamsByName->get(mb_strtolower(trim((string) $project->name)));

            if ($matched instanceof Team) {
                $grouped[$matched->id] = $grouped[$matched->id]->push($project)->unique('id')->values();

                continue;
            }

            foreach ($teams as $team) {
                if ($project->user_id && $team->users->contains('id', (int) $project->user_id)) {
                    $grouped[$team->id] = $grouped[$team->id]->push($project)->unique('id')->values();
                }
            }
        }

        return $grouped;
    }

    /**
     * @param  array<int, int>  $projectIds
     * @return array<int, array{total: int, done: int}>
     */
    protected function taskStats(array $projectIds): array
    {
        if ($projectIds === [] || ! Schema::hasTable('projects_tasks')) {
            return [];
        }

        return DB::table('projects_tasks')
            ->whereIn('project_id', $projectIds)
            ->whereNull('deleted_at')
            ->groupBy('project_id')
            ->selectRaw("project_id, count(*) as total, sum(case when state in ('done', 'approved') then 1 else 0 end) as done")
            ->get()
            ->mapWithKeys(fn ($row): array => [
                (int) $row->project_id => [
                    'total' => (int) $row->total,
                    'done'  => (int) $row->done,
                ],
            ])
            ->all();
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @param  array<int, array{total: int, done: int}>  $taskStats
     * @return array<string, mixed>
     */
    protected function presentCard(Team $team, Collection $projects, array $taskStats, bool $indonesian): array
    {
        $totalTasks = 0;
        $doneTasks = 0;
        $value = 0.0;
        $latest = $team->updated_at instanceof Carbon ? $team->updated_at : null;

        foreach ($projects as $project) {
            $stats = $taskStats[$project->id] ?? ['total' => 0, 'done' => 0];
            $totalTasks += $stats['total'];
            $doneTasks += $stats['done'];
            $value += (float) $project->project_value;

            if ($project->updated_at instanceof Carbon && ($latest === null || $project->updated_at->gt($latest))) {
                $latest = $project->updated_at;
            }
        }

        // Contract values are financial data: only Admin and the
        // budget-control team see the aggregate value on team cards.
        $canViewProjectValue = false;
        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (class_exists($scopeClass)) {
            $canViewProjectValue = app($scopeClass)->canViewProjectValue();
        }

        $lead = $projects->first();
        $about = trim(strip_tags((string) ($lead?->description ?: '')));

        if ($about === '') {
            $about = $projects->isEmpty()
                ? ($indonesian ? 'Tim ini belum memiliki proyek aktif.' : 'This team has no active project yet.')
                : $projects->pluck('name')->take(3)->implode(', ').'.';
        }

        $members = $this->presentMembers($team, $projects, $indonesian);
        $projectLinks = $projects->map(fn (Project $project): array => [
            'id'   => $project->id,
            'name' => $project->name,
            'href' => $this->projectViewUrl($project),
        ])->values()->all();

        return [
            'id'                 => $team->id,
            'name'               => $team->name,
            'is_new'             => $team->created_at instanceof Carbon && $team->created_at->gte(now()->subDays(14)),
            'member_count'       => count($members),
            'extra_members'      => max(count($members) - 6, 0),
            'members'            => array_slice($members, 0, 6),
            'members_all'        => $members,
            'about'              => Str::limit($about, 180),
            'project_count'      => $projects->count(),
            'projects'           => $projectLinks,
            'primary_project_id' => $lead?->id,
            'can_edit_members'   => static::managesTeams() && $projects->isNotEmpty(),
            'task_count'         => $totalTasks,
            'value'              => $canViewProjectValue ? ($value > 0 ? 'Rp '.number_format($value, 0, ',', '.') : '—') : null,
            'value_amount'       => $canViewProjectValue ? $value : null,
            'progress'           => $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0,
            'updated'            => $latest?->locale(app()->getLocale())->diffForHumans() ?? '—',
            'updated_ts'         => $latest?->getTimestamp() ?? 0,
            'href'               => url('/admin/teams'),
        ];
    }

    /**
     * Roster for the board is employees on linked projects (including those
     * without a Related User). A Project Manager is shown only if they have
     * an employee record. Login-only team accounts are not listed here.
     *
     * @param  Collection<int, Project>  $projects
     * @return array<int, array{employee_id: ?int, name: string, initials: string, color: string, role: string, href: ?string, projects: string, has_login: bool, can_remove: bool}>
     */
    protected function presentMembers(Team $team, Collection $projects, bool $indonesian): array
    {
        $palette = ['#6366f1', '#0ea5e9', '#14b8a6', '#f59e0b', '#ec4899', '#8b5cf6'];
        $roleManager = $indonesian ? 'Manajer Proyek' : 'Project Manager';
        $roleAssigned = $indonesian ? 'Ditugaskan ke proyek' : 'Assigned to project';
        $seen = [];
        $members = [];
        $canEdit = static::managesTeams() && $projects->isNotEmpty();

        foreach ($projects as $project) {
            if ($project->user instanceof User) {
                $managerEmployee = $this->employeeForUser((int) $project->user->id);

                if ($managerEmployee instanceof Employee) {
                    $key = 'employee:'.$managerEmployee->id;

                    if (! isset($seen[$key])) {
                        $seen[$key] = count($members);
                        $members[] = $this->memberChip(
                            $managerEmployee->name,
                            count($members),
                            $palette,
                            $roleManager,
                            $this->employeeViewUrl($managerEmployee),
                            [$project->name],
                            $managerEmployee->id,
                            filled($managerEmployee->user_id),
                            false,
                        );
                    } else {
                        $members[$seen[$key]]['projects'] = $this->appendProjectLabel(
                            $members[$seen[$key]]['projects'],
                            $project->name,
                        );
                        $members[$seen[$key]]['role'] = $roleManager;
                    }
                }
            }

            foreach ($project->employees ?? [] as $employee) {
                if (! $employee instanceof Employee) {
                    continue;
                }

                $key = 'employee:'.$employee->id;
                $isManager = $project->user_id && (int) $employee->user_id === (int) $project->user_id;

                if (! isset($seen[$key])) {
                    $seen[$key] = count($members);
                    $members[] = $this->memberChip(
                        $employee->name,
                        count($members),
                        $palette,
                        $isManager ? $roleManager : $roleAssigned,
                        $this->employeeViewUrl($employee),
                        [$project->name],
                        $employee->id,
                        filled($employee->user_id),
                        $canEdit && ! $isManager,
                    );
                } else {
                    $members[$seen[$key]]['projects'] = $this->appendProjectLabel(
                        $members[$seen[$key]]['projects'],
                        $project->name,
                    );
                    $members[$seen[$key]]['can_remove'] = $canEdit && $members[$seen[$key]]['role'] !== $roleManager;
                }
            }
        }

        return $members;
    }

    /**
     * @param  array<int, string>  $palette
     * @param  array<int, string>  $projectNames
     * @return array{employee_id: ?int, name: string, initials: string, color: string, role: string, href: ?string, projects: string, has_login: bool, can_remove: bool}
     */
    protected function memberChip(
        string $name,
        int $index,
        array $palette,
        string $role,
        ?string $href,
        array $projectNames,
        ?int $employeeId = null,
        bool $hasLogin = true,
        bool $canRemove = false,
    ): array {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= Str::upper(Str::substr($part, 0, 1));
        }

        return [
            'employee_id' => $employeeId,
            'name'        => $name,
            'initials'    => $initials !== '' ? $initials : '?',
            'color'       => $palette[$index % count($palette)],
            'role'        => $role,
            'href'        => $href,
            'projects'    => implode(', ', array_values(array_unique($projectNames))),
            'has_login'   => $hasLogin,
            'can_remove'  => $canRemove,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cards
     * @return array<int, array{id: int, name: string, has_login: bool}>
     */
    protected function candidateEmployeesForEditor(array $cards): array
    {
        if (! static::managesTeams() || ! $this->editingTeamId || ! class_exists(Employee::class)) {
            return [];
        }

        $card = collect($cards)->firstWhere('id', $this->editingTeamId);

        if ($card === null) {
            return [];
        }

        $assignedIds = collect($card['members_all'] ?? [])
            ->pluck('employee_id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->all();

        return Employee::query()
            ->where('is_active', true)
            ->when($assignedIds !== [], fn ($query) => $query->whereNotIn('id', $assignedIds))
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name', 'user_id'])
            ->map(fn (Employee $employee): array => [
                'id'        => $employee->id,
                'name'      => $employee->name,
                'has_login' => filled($employee->user_id),
            ])
            ->all();
    }

    protected function employeeForUser(int $userId): ?Employee
    {
        if (! class_exists(Employee::class) || $userId < 1) {
            return null;
        }

        return Employee::query()
            ->where('user_id', $userId)
            ->first(['id', 'name', 'user_id']);
    }

    protected function appendProjectLabel(string $current, string $projectName): string
    {
        $parts = array_filter(array_map('trim', explode(',', $current)));

        if (! in_array($projectName, $parts, true)) {
            $parts[] = $projectName;
        }

        return implode(', ', $parts);
    }

    protected function projectViewUrl(Project $project): string
    {
        try {
            return ProjectResource::getUrl('view', ['record' => $project]);
        } catch (Throwable) {
            return url('/admin/project/projects/'.$project->getRouteKey());
        }
    }

    protected function employeeViewUrl(Employee $employee): ?string
    {
        try {
            if (! class_exists(EmployeeResource::class)) {
                return null;
            }

            return EmployeeResource::getUrl('view', ['record' => $employee]);
        } catch (Throwable) {
            return url('/admin/employees/employees/'.$employee->getRouteKey());
        }
    }
}
