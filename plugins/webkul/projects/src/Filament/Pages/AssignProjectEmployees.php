<?php

namespace Webkul\Project\Filament\Pages;

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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Services\EmployeeUserProvisioner;
use Webkul\PluginManager\Package;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Support\Enums\NavigationGroup;

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
                            ->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->native()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                $projectId = filled($state) ? (int) $state : null;

                                if (! $projectId) {
                                    $set('employee_ids', []);

                                    return;
                                }

                                $employeeIds = Project::query()
                                    ->find($projectId)
                                    ?->employees()
                                    ->pluck('employees_employees.id')
                                    ->map(fn ($id): int => (int) $id)
                                    ->all() ?? [];

                                $set('employee_ids', $employeeIds);
                            }),
                        CheckboxList::make('employee_ids')
                            ->label(__('projects::filament/pages/assign-project-employees.fields.employees'))
                            ->helperText(__('projects::filament/pages/assign-project-employees.fields.employees-helper'))
                            ->options(function (Get $get): array {
                                if (blank($get('project_id'))) {
                                    return [];
                                }

                                return $this->employeeOptions();
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->visible(fn (Get $get): bool => filled($get('project_id')))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
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
     * Lightweight labels only — capacity lookups on every active employee caused
     * 500s on production when the workforce list is large.
     *
     * @return array<int, string>
     */
    protected function employeeOptions(): array
    {
        return Employee::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'user_id'])
            ->mapWithKeys(function (Employee $employee): array {
                $suffix = $employee->user_id ? '' : ' (no login)';

                return [$employee->id => $employee->name.$suffix];
            })
            ->all();
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $projectId = $state['project_id'] ?? null;

        if (! $projectId) {
            Notification::make()
                ->danger()
                ->title(__('projects::filament/pages/assign-project-employees.notifications.missing-project.title'))
                ->send();

            return;
        }

        $project = Project::query()->findOrFail($projectId);
        $employeeIds = array_values(array_filter(
            Arr::wrap($state['employee_ids'] ?? []),
            fn (mixed $id): bool => filled($id),
        ));

        $result = app(ProjectEmployeeAssigner::class)->sync(
            $project,
            $employeeIds,
        );

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
