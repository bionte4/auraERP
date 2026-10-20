<?php

namespace Webkul\Project\Filament\Pages;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Services\EmployeeUserProvisioner;
use Webkul\PluginManager\Package;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\OperationalScope;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\NavigationBadge;

class AssignProjectEmployees extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'project/assign-employees';

    protected string $view = 'projects::filament.pages.assign-project-employees';

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_project_assign_project_employees';
    }

    public static function canAccess(): bool
    {
        if (! Package::isPluginInstalled('employees')) {
            return false;
        }

        return parent::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationBadge(): ?string
    {
        return NavigationBadge::count('employees-without-user', fn (): int => Employee::query()
            ->where('is_active', true)
            ->whereNull('user_id')
            ->count());
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('projects::filament/pages/assign-project-employees.navigation.badge-tooltip');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/assign-project-employees.navigation.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon;
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/assign-project-employees.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'project_id'   => null,
            'employee_ids' => [],
        ]);
    }

    /**
     * Keep CheckboxList selection in sync when the native project select changes.
     * (Avoid relying on afterStateUpdated + visible() — selection was lost on VPS.)
     */
    public function updatedDataProjectId(mixed $value): void
    {
        $this->data['employee_ids'] = $this->assignedEmployeeIdsForProject($value);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('projects::filament/pages/assign-project-employees.title'))
                    ->description(__('projects::filament/pages/assign-project-employees.description'))
                    ->schema([
                        // Native <select> — searchable()/multiple() force Alpine selects that
                        // fail to initialize on this custom page (empty non-clickable boxes).
                        Select::make('project_id')
                            ->label(__('projects::filament/pages/assign-project-employees.fields.project'))
                            ->options(fn (): array => Project::query()
                                ->when($this->scopedProjectIds() !== null, fn ($query) => $query->whereIn('id', $this->scopedProjectIds()))
                                ->orderBy('name')->pluck('name', 'id')->all())
                            ->native()
                            ->required()
                            ->live(),
                        LivewireSearchableCheckboxList::searchInput('employee_ids_search'),
                        CheckboxList::make('employee_ids')
                            ->label(__('projects::filament/pages/assign-project-employees.fields.employees'))
                            ->helperText(__('projects::filament/pages/assign-project-employees.fields.employees-helper'))
                            ->options(fn (Get $get): array => LivewireSearchableCheckboxList::filterOptions(
                                $this->employeeOptions(),
                                $get,
                                'employee_ids',
                            ))
                            ->columns(2)
                            ->disabled(fn (Get $get): bool => blank($get('project_id')))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    /**
     * Project IDs a scoped user (e.g. Operation Manager) may assign
     * employees to, or null when unscoped (Admin/HRD).
     *
     * @return array<int, int>|null
     */
    protected function scopedProjectIds(): ?array
    {
        if (! auth()->check()) {
            return null;
        }

        $scope = app(OperationalScope::class);

        if ($scope->isUnscoped()) {
            return null;
        }

        return $scope->projectIds();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('provisionMissingUsers')
                ->label(__('projects::filament/pages/assign-project-employees.actions.provision-users'))
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('projects::filament/pages/assign-project-employees.actions.provision-heading'))
                ->modalDescription(__('projects::filament/pages/assign-project-employees.actions.provision-description'))
                ->action(function (): void {
                    $result = app(EmployeeUserProvisioner::class)->provisionMissingForAssignedEmployees();

                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/pages/assign-project-employees.notifications.provisioned.title'))
                        ->body(__('projects::filament/pages/assign-project-employees.notifications.provisioned.body', [
                            'count' => $result['provisioned'],
                            'skip'  => $result['skipped'],
                        ]))
                        ->send();
                }),
            Action::make('save')
                ->label(__('projects::filament/pages/assign-project-employees.actions.save'))
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(fn () => $this->save()),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function employeeOptions(): array
    {
        return Employee::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->mapWithKeys(function (Employee $employee): array {
                $suffix = $employee->user_id ? '' : ' (no login)';

                // String keys — CheckboxList + Livewire compare selections as strings.
                return [(string) $employee->id => $employee->name.$suffix];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function assignedEmployeeIdsForProject(mixed $projectId): array
    {
        if (blank($projectId)) {
            return [];
        }

        $project = Project::query()->find((int) $projectId);

        if (! $project) {
            return [];
        }

        return $project->employees()
            ->pluck('employees_employees.id')
            ->map(fn ($id): string => (string) (int) $id)
            ->values()
            ->all();
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $projectId = $state['project_id'] ?? ($this->data['project_id'] ?? null);

        if (! $projectId) {
            Notification::make()
                ->danger()
                ->title(__('projects::filament/pages/assign-project-employees.notifications.missing-project.title'))
                ->send();

            return;
        }

        $project = Project::query()->findOrFail($projectId);

        $scopedProjectIds = $this->scopedProjectIds();

        if ($scopedProjectIds !== null && ! in_array((int) $project->id, $scopedProjectIds, true)) {
            Notification::make()
                ->danger()
                ->title(__('projects::filament/pages/assign-project-employees.notifications.missing-project.title'))
                ->send();

            return;
        }

        $employeeIds = array_values(array_filter(
            Arr::wrap($state['employee_ids'] ?? ($this->data['employee_ids'] ?? [])),
            fn (mixed $id): bool => filled($id),
        ));

        $result = app(ProjectEmployeeAssigner::class)->sync(
            $project,
            $employeeIds,
        );

        // Refresh checkbox state from DB after save.
        $this->data['employee_ids'] = $this->assignedEmployeeIdsForProject($project->id);

        $notification = Notification::make()
            ->success()
            ->title(__('projects::filament/pages/assign-project-employees.notifications.saved.title'));

        if ($result['without_user'] > 0) {
            $notification
                ->warning()
                ->body(__('projects::filament/pages/assign-project-employees.notifications.saved.body-warn', [
                    'count'   => $result['assigned'],
                    'without' => $result['without_user'],
                ]));
        } else {
            $notification->body(__('projects::filament/pages/assign-project-employees.notifications.saved.body', [
                'count' => $result['assigned'],
            ]));
        }

        $notification->send();
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getAssignedEmployeesProperty(): Collection
    {
        $projectId = $this->data['project_id'] ?? null;

        if (! $projectId) {
            return collect();
        }

        $project = Project::query()->find($projectId);

        if (! $project) {
            return collect();
        }

        return app(ProjectEmployeeAssigner::class)->assignedEmployees($project);
    }
}
