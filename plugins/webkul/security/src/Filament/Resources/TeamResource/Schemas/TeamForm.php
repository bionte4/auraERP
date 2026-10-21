<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema as DbSchema;
use Illuminate\Support\HtmlString;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Security\Models\Team;
use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;

class TeamForm
{
    /**
     * Employee IDs currently on the team's linked project(s).
     *
     * @return array<int, int>
     */
    public static function assignedEmployeeIds(?Team $record): array
    {
        if (! $record || ! class_exists(Project::class) || ! DbSchema::hasTable('projects_project_employees')) {
            return [];
        }

        $ids = [];

        foreach (static::projectsForTeam($record) as $project) {
            foreach ($project->employees as $employee) {
                $ids[] = (int) $employee->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array<int, int>
     */
    public static function employeeIdsForProject(?int $projectId, ?string $teamName = null): array
    {
        if (! class_exists(Project::class) || ! DbSchema::hasTable('projects_project_employees')) {
            return [];
        }

        $query = Project::query()->withoutGlobalScopes()->whereNull('deleted_at');

        if ($projectId) {
            $project = $query->whereKey($projectId)->with('employees')->first();

            return $project instanceof Project
                ? $project->employees->pluck('id')->map(fn ($id): int => (int) $id)->all()
                : [];
        }

        $name = trim((string) $teamName);

        if ($name === '') {
            return [];
        }

        return $query
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->with('employees')
            ->get()
            ->flatMap(fn (Project $project) => $project->employees->pluck('id'))
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int|string>  $employeeIds
     * @return array<int, string>
     */
    public static function employeeLabels(array $employeeIds): array
    {
        if (! class_exists(Employee::class) || $employeeIds === []) {
            return [];
        }

        $ids = collect($employeeIds)->map(fn ($id): int => (int) $id)->filter()->all();

        return Employee::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->map(function (Employee $employee): string {
                $label = (string) $employee->name;

                if (! $employee->user_id) {
                    $label .= ' ('.__('security::filament/resources/team.form.fields.no-login').')';
                }

                return $label;
            })
            ->all();
    }

    /**
     * Persist employee roster onto linked project(s). Employees with a
     * Related User also land on user_team via TeamMembershipSynchronizer.
     *
     * @param  array<int, int|string>  $employeeIds
     */
    public static function syncEmployees(Team $team, array $employeeIds): void
    {
        if (! class_exists(ProjectEmployeeAssigner::class) || ! class_exists(Project::class)) {
            return;
        }

        $ids = collect($employeeIds)
            ->filter(fn (mixed $id): bool => filled($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $projects = static::projectsForTeam($team);

        if ($projects === []) {
            static::syncLoginUsersFromEmployees($team, $ids);

            return;
        }

        $assigner = app(ProjectEmployeeAssigner::class);

        foreach ($projects as $project) {
            $assigner->sync($project, $ids);
        }
    }

    /**
     * @return array<int, Project>
     */
    public static function projectsForTeam(Team $team): array
    {
        if (! class_exists(Project::class) || ! DbSchema::hasTable('projects_projects')) {
            return [];
        }

        $query = Project::query()->withoutGlobalScopes()->whereNull('deleted_at');

        if (DbSchema::hasColumn('teams', 'project_id') && $team->project_id) {
            $linked = (clone $query)->whereKey($team->project_id)->first();

            return $linked instanceof Project ? [$linked->load('employees')] : [];
        }

        $name = trim((string) $team->name);

        if ($name === '') {
            return [];
        }

        return $query
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->with('employees')
            ->get()
            ->all();
    }

    /**
     * @param  array<int, int>  $employeeIds
     */
    protected static function syncLoginUsersFromEmployees(Team $team, array $employeeIds): void
    {
        if (! class_exists(Employee::class) || $employeeIds === []) {
            $team->users()->sync([]);

            return;
        }

        $userIds = Employee::query()
            ->whereIn('id', $employeeIds)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $team->users()->sync($userIds);
    }

    /**
     * @return array<int, string>
     */
    protected static function employeeOptions(): array
    {
        if (! class_exists(Employee::class) || ! DbSchema::hasTable('employees_employees')) {
            return [];
        }

        return Employee::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->mapWithKeys(function (Employee $employee): array {
                $label = (string) $employee->name;

                if (! $employee->user_id) {
                    $label .= ' ('.__('security::filament/resources/team.form.fields.no-login').')';
                }

                return [$employee->id => $label];
            })
            ->all();
    }

    /**
     * Without a search term, only currently assigned employees are listed
     * as checkboxes. Search reveals other employees to add.
     *
     * @return array<int, string>
     */
    protected static function checkboxEmployeeOptions(Get $get): array
    {
        $all = static::employeeOptions();
        $selected = array_map('strval', Arr::wrap($get('employees') ?? []));
        $search = trim((string) $get('employees_search'));

        if ($search !== '') {
            return $all;
        }

        return collect($all)
            ->filter(fn (string $label, mixed $id): bool => in_array((string) $id, $selected, true))
            ->all();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('security::filament/resources/team.form.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('project_id')
                    ->label(__('security::filament/resources/team.form.fields.project'))
                    ->helperText(__('security::filament/resources/team.form.fields.project-help'))
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state, Get $get): void {
                        $set('employees', static::employeeIdsForProject(
                            filled($state) ? (int) $state : null,
                            $get('name'),
                        ));
                    })
                    ->visible(fn (): bool => class_exists(Project::class)
                        && DbSchema::hasTable('projects_projects')
                        && DbSchema::hasColumn('teams', 'project_id'))
                    ->columnSpanFull(),
                Placeholder::make('current_project_members')
                    ->label(__('security::filament/resources/team.form.fields.current-members'))
                    ->content(function (Get $get): HtmlString {
                        $labels = static::employeeLabels(Arr::wrap($get('employees') ?? []));

                        if ($labels === []) {
                            return new HtmlString(e(__('security::filament/resources/team.form.fields.current-members-empty')));
                        }

                        $items = collect($labels)
                            ->map(fn (string $label): string => '<li>'.e($label).'</li>')
                            ->implode('');

                        return new HtmlString('<ul class="list-disc space-y-1 ps-5 text-sm">'.$items.'</ul>');
                    })
                    ->columnSpanFull(),
                ...LivewireSearchableCheckboxList::fromOptions(
                    name: 'employees',
                    options: fn (Get $get): array => static::checkboxEmployeeOptions($get),
                    label: __('security::filament/resources/team.form.fields.members'),
                    helperText: __('security::filament/resources/team.form.fields.members-help'),
                    searchPlaceholder: __('security::filament/resources/team.form.fields.members-search'),
                    columns: 2,
                    configure: fn ($list) => $list
                        ->dehydrated()
                        ->live()
                        ->afterStateHydrated(function ($component, ?Team $record): void {
                            $component->state(static::assignedEmployeeIds($record));
                        }),
                ),
                Placeholder::make('members_scope_note')
                    ->hiddenLabel()
                    ->content(__('security::filament/resources/team.form.fields.members-note'))
                    ->columnSpanFull(),
            ]);
    }
}
