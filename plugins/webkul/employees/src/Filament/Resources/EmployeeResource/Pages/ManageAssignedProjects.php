<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Services\OperationalScope;
use Webkul\PluginManager\Package;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;

class ManageAssignedProjects extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = EmployeeResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected string $view = 'employees::filament.resources.employee-resource.pages.manage-assigned-projects';

    public ?array $data = [];

    public static function canAccess(array $parameters = []): bool
    {
        if (! Package::isPluginInstalled('projects') || ! class_exists(Project::class)) {
            return false;
        }

        if (! SchemaFacade::hasTable('projects_project_employees')) {
            return false;
        }

        return parent::canAccess($parameters);
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-assigned-projects.navigation.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('employees::filament/resources/employee/pages/manage-assigned-projects.title', [
            'name' => $this->getRecord()->name,
        ]);
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);

        $this->form->fill([
            'project_ids' => $this->getRecord()
                ->projects()
                ->pluck('projects_projects.id')
                ->map(fn ($id): int => (int) $id)
                ->all(),
        ]);
    }

    /**
     * Project IDs a scoped user (e.g. Operation Manager) may assign to,
     * or null when the current user is unscoped (Admin/HRD).
     *
     * @return array<int, int>|null
     */
    protected function scopedProjectIds(): ?array
    {
        if (! auth()->check() || ! class_exists(OperationalScope::class)) {
            return null;
        }

        $scope = app(OperationalScope::class);

        if ($scope->isUnscoped()) {
            return null;
        }

        return $scope->projectIds();
    }

    public function form(Schema $schema): Schema
    {
        $scopedProjectIds = $this->scopedProjectIds();
        $assignedProjectIds = $this->getRecord()
            ->projects()
            ->pluck('projects_projects.id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        return $schema
            ->components([
                Section::make(__('employees::filament/resources/employee/pages/manage-assigned-projects.section.title'))
                    ->description(__('employees::filament/resources/employee/pages/manage-assigned-projects.section.description'))
                    ->schema([
                        LivewireSearchableCheckboxList::searchInput('project_ids_search'),
                        CheckboxList::make('project_ids')
                            ->label(__('employees::filament/resources/employee/pages/manage-assigned-projects.fields.projects'))
                            ->helperText(__('employees::filament/resources/employee/pages/manage-assigned-projects.fields.projects-helper'))
                            ->options(fn (Get $get): array => LivewireSearchableCheckboxList::filterOptions(
                                Project::query()
                                    ->when(
                                        $scopedProjectIds !== null,
                                        fn ($query) => $query->whereIn('id', array_values(array_unique([...$scopedProjectIds, ...$assignedProjectIds]))),
                                    )
                                    ->orderBy('name')->pluck('name', 'id')->all(),
                                $get,
                                'project_ids',
                            ))
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('employees::filament/resources/employee/pages/manage-assigned-projects.actions.save'))
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        /** @var Employee $employee */
        $employee = $this->getRecord();
        $state = $this->form->getState();

        $projectIds = $state['project_ids'] ?? [];

        // Scoped users only see their own projects in the list; keep any
        // existing assignments outside their scope untouched on save.
        $scopedProjectIds = $this->scopedProjectIds();

        if ($scopedProjectIds !== null) {
            $existingProjectIds = $employee->projects()
                ->pluck('projects_projects.id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            $preserved = array_diff($existingProjectIds, $scopedProjectIds);

            $projectIds = array_values(array_unique([
                ...array_map('intval', Arr::wrap($projectIds)),
                ...$preserved,
            ]));
        }

        $result = app(ProjectEmployeeAssigner::class)->syncProjectsForEmployee(
            $employee,
            $projectIds,
        );

        $notification = Notification::make()
            ->success()
            ->title(__('employees::filament/resources/employee/pages/manage-assigned-projects.notifications.saved.title'));

        if ($result['without_user'] > 0) {
            $notification
                ->warning()
                ->body(__('employees::filament/resources/employee/pages/manage-assigned-projects.notifications.saved.body-warn', [
                    'count' => $result['assigned'],
                ]));
        } else {
            $notification->body(__('employees::filament/resources/employee/pages/manage-assigned-projects.notifications.saved.body', [
                'count' => $result['assigned'],
            ]));
        }

        $notification->send();
    }

    /**
     * @return Collection<int, Project>
     */
    public function getAssignedProjectsProperty(): Collection
    {
        return app(ProjectEmployeeAssigner::class)->assignedProjects($this->getRecord());
    }
}
