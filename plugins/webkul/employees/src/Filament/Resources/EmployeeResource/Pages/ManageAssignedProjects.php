<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Models\Employee;
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('employees::filament/resources/employee/pages/manage-assigned-projects.section.title'))
                    ->description(__('employees::filament/resources/employee/pages/manage-assigned-projects.section.description'))
                    ->schema([
                        // CheckboxList — Select multiple/searchable forces Alpine and can render empty.
                        CheckboxList::make('project_ids')
                            ->label(__('employees::filament/resources/employee/pages/manage-assigned-projects.fields.projects'))
                            ->helperText(__('employees::filament/resources/employee/pages/manage-assigned-projects.fields.projects-helper'))
                            ->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->bulkToggleable()
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

        $result = app(ProjectEmployeeAssigner::class)->syncProjectsForEmployee(
            $employee,
            $state['project_ids'] ?? [],
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
