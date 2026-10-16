<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

class TeamBoard extends Page
{
    protected string $view = 'filament.pages.team-board';

    protected static ?string $slug = 'team-board';

    protected static ?int $navigationSort = -8;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole')) {
            return false;
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        return $user->hasRole($adminRole)
            || $user->hasRole('Operation Manager')
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

    /**
     * @return array<string, mixed>
     */
    public function getBoardData(): array
    {
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $manages = static::managesTeams();
        $cards = $this->cardsFor(auth()->user());

        return [
            'manages'  => $manages,
            'title'    => $manages
                ? ($indonesian ? 'Kelola Tim' : 'Manage Teams')
                : ($indonesian ? 'Tim Saya' : 'My Team'),
            'subtitle' => $manages
                ? ($indonesian
                    ? 'Pantau anggota, proyek berjalan, dan nilai proyek pada tim yang Anda kelola.'
                    : 'Track members, active projects, and project value for the teams you manage.')
                : ($indonesian
                    ? 'Proyek dan kemajuan tim tempat Anda menjadi anggota.'
                    : 'Projects and progress for the team you belong to.'),
            'action'   => $this->headerAction($indonesian),
            'cards'    => $cards,
            'empty'    => $manages
                ? ($indonesian
                    ? 'Belum ada tim. Buat tim, lalu tambahkan anggota supaya proyek mereka muncul di papan ini.'
                    : 'No team yet. Create a team and add members so their projects appear on this board.')
                : ($indonesian
                    ? 'Anda belum masuk ke sebuah tim. Minta Operation Manager menambahkan Anda.'
                    : 'You are not on a team yet. Ask an Operation Manager to add you.'),
            'labels'   => [
                'members'  => $indonesian ? 'anggota' : 'members',
                'about'    => $indonesian ? 'Tentang tim' : 'About team',
                'projects' => $indonesian ? 'Proyek' : 'Projects',
                'tasks'    => $indonesian ? 'Tugas' : 'Tasks',
                'value'    => $indonesian ? 'Nilai' : 'Value',
                'status'   => $indonesian ? 'Status proyek berjalan' : 'Status of current projects',
                'updated'  => $indonesian ? 'Diperbarui' : 'Updated',
                'details'  => $indonesian ? 'Detail' : 'Details',
                'new'      => $indonesian ? 'Baru' : 'New',
            ],
        ];
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
        $primaryTeamByUser = $this->primaryTeamByUser($allTeams);
        $teams = $user->hasRole($adminRole)
            ? $allTeams
            : $allTeams->filter(function (Team $team) use ($user): bool {
                return (int) $team->creator_id === (int) $user->id
                    || $team->users->contains('id', $user->id);
            })->values();

        if ($teams->isEmpty()) {
            return [];
        }
        $projects = $this->projectsFor(array_keys($primaryTeamByUser));
        $taskStats = $this->taskStats($projects->pluck('id')->all());

        return $teams->map(function (Team $team) use ($primaryTeamByUser, $projects, $taskStats): array {
            $teamProjects = $projects
                ->filter(fn (Project $project): bool => ($primaryTeamByUser[(int) $project->user_id] ?? null) === $team->id)
                ->values();

            return $this->presentCard($team, $teamProjects, $taskStats, str_starts_with((string) app()->getLocale(), 'id'));
        })->all();
    }

    /**
     * @param  Collection<int, Team>  $teams
     * @return array<int, int>
     */
    protected function primaryTeamByUser(Collection $teams): array
    {
        $primary = [];

        foreach ($teams as $team) {
            foreach ($team->users as $member) {
                $primary[(int) $member->id] ??= $team->id;
            }
        }

        return $primary;
    }

    /**
     * @param  array<int, int>  $userIds
     * @return Collection<int, Project>
     */
    protected function projectsFor(array $userIds): Collection
    {
        if ($userIds === [] || ! Schema::hasTable('projects_projects')) {
            return collect();
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereIn('user_id', $userIds)
            ->orderByDesc('updated_at')
            ->get();
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

        $lead = $projects->first();
        $about = trim(strip_tags((string) ($lead?->description ?: '')));

        if ($about === '') {
            $about = $projects->isEmpty()
                ? ($indonesian ? 'Tim ini belum memiliki proyek aktif.' : 'This team has no active project yet.')
                : $projects->pluck('name')->take(3)->implode(', ').'.';
        }

        $palette = ['#6366f1', '#0ea5e9', '#14b8a6', '#f59e0b', '#ec4899', '#8b5cf6'];
        $members = $team->users->take(4)->values()->map(function (User $member, int $index) use ($palette): array {
            $parts = preg_split('/\s+/', trim($member->name)) ?: [];
            $initials = '';

            foreach (array_slice($parts, 0, 2) as $part) {
                $initials .= Str::upper(Str::substr($part, 0, 1));
            }

            return [
                'name'     => $member->name,
                'initials' => $initials !== '' ? $initials : '?',
                'color'    => $palette[$index % count($palette)],
            ];
        })->all();

        return [
            'name'          => $team->name,
            'is_new'        => $team->created_at instanceof Carbon && $team->created_at->gte(now()->subDays(14)),
            'member_count'  => $team->users->count(),
            'extra_members' => max($team->users->count() - 4, 0),
            'members'       => $members,
            'about'         => Str::limit($about, 180),
            'project_count' => $projects->count(),
            'task_count'    => $totalTasks,
            'value'         => $value > 0 ? 'Rp '.number_format($value, 0, ',', '.') : '—',
            'progress'      => $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0,
            'updated'       => $latest?->locale(app()->getLocale())->diffForHumans() ?? '—',
            'href'          => $lead ? url('/admin/project/projects/'.$lead->id) : url('/admin/project/projects'),
        ];
    }
}
