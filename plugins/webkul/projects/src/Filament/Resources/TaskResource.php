<?php

namespace Webkul\Project\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Filament\Resources\TaskResource\Pages\CreateTask;
use Webkul\Project\Filament\Resources\TaskResource\Pages\EditTask;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ListTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ManageSubTasks;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ManageTimesheets;
use Webkul\Project\Filament\Resources\TaskResource\Pages\ViewTask;
use Webkul\Project\Filament\Resources\TaskResource\RelationManagers\SubTasksRelationManager;
use Webkul\Project\Filament\Resources\TaskResource\RelationManagers\TimesheetsRelationManager;
use Webkul\Project\Filament\Resources\TaskResource\Schemas\TaskForm;
use Webkul\Project\Filament\Resources\TaskResource\Schemas\TaskInfolist;
use Webkul\Project\Filament\Resources\TaskResource\Tables\TasksTable;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Project\Settings\TaskSettings;
use Webkul\Project\Settings\TimeSettings;
use Webkul\Support\Enums\NavigationGroup;

class TaskResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    use HasCustomFields;

    protected static ?string $model = Task::class;

    protected static ?string $slug = 'project/tasks';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/resources/task.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'project.name', 'partner.name', 'milestone.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('projects::filament/resources/task.global-search.project')   => $record->project?->name ?? '—',
            __('projects::filament/resources/task.global-search.customer')  => $record->partner?->name ?? '—',
            __('projects::filament/resources/task.global-search.milestone') => $record->milestone?->name ?? '—',
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        // Tasks follow the same project scope as the Projects list:
        // Operation Managers, Team Leaders, and plain Staff only see
        // tasks of projects assigned to them; Admin/HRD and
        // Accounting keep the full list. As on the Projects list, the
        // OwnershipScope global scope is lifted for those full-list
        // roles so a Group / Individual Resource Permission cannot
        // silently hide tasks of projects they do not own.
        $user = auth()->user();

        if ($user && class_exists(\Webkul\Project\Services\OperationalScope::class)) {
            $scope = app(\Webkul\Project\Services\OperationalScope::class);
            $projectIds = $scope->projectListScopeIds($user);

            if ($projectIds === null) {
                if ($scope->bypassesListOwnershipScope($user)) {
                    $query->withoutGlobalScope(\Webkul\Security\Models\Scopes\OwnershipScope::class);
                }

                return $query;
            }

            $projectIds === []
                ? $query->whereRaw('1 = 0')
                : $query->whereIn($query->getModel()->getTable().'.project_id', $projectIds);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table, static::getCustomTableColumns(), static::getTableQueryBuilderConstraints());
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getDefaultStageId($projectId): ?int
    {
        if (! $projectId) {
            return null;
        }

        return TaskStage::query()
            ->where('project_id', $projectId)
            ->orderBy('sort')
            ->first()?->id;
    }

    public static function getTimeSettings(): TimeSettings
    {
        return settings(TimeSettings::class);
    }

    public static function getTaskSettings(): TaskSettings
    {
        return settings(TaskSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewTask::class,
            EditTask::class,
            ManageTimesheets::class,
            ManageSubTasks::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Timesheets', [
                TimesheetsRelationManager::class,
            ])
                ->icon('heroicon-o-clock'),

            RelationGroup::make('Sub Tasks', [
                SubTasksRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListTasks::route('/'),
            'create'     => CreateTask::route('/create'),
            'edit'       => EditTask::route('/{record}/edit'),
            'view'       => ViewTask::route('/{record}'),
            'timesheets' => ManageTimesheets::route('/{record}/timesheets'),
            'sub-tasks'  => ManageSubTasks::route('/{record}/sub-tasks'),
        ];
    }
}
