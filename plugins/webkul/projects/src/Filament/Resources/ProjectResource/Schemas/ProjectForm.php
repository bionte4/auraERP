<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Schemas;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Project\Enums\ProjectVisibility;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Security\Filament\Resources\CompanyResource;
use Webkul\Security\Filament\Resources\UserResource;

class ProjectForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        FormProgressStepper::make('stage_id')
                            ->hiddenLabel()
                            ->inline()
                            ->required()
                            ->visible(ProjectResource::getTaskSettings()->enable_project_stages)
                            ->options(fn () => ProjectStage::orderBy('sort')->get()->mapWithKeys(fn ($stage) => [$stage->id => $stage->name]))
                            ->default(fn () => static::getDefaultStageId(current_company_id())),
                        Section::make(__('projects::filament/resources/project.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('projects::filament/resources/project.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                RichEditor::make('description')
                                    ->label(__('projects::filament/resources/project.form.sections.general.fields.description')),
                            ]),

                        Section::make(__('projects::filament/resources/project.form.sections.additional.title'))
                            ->schema(array_merge([
                                Select::make('user_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.project-manager'))
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => UserResource::form($schema)),
                                Select::make('team_leader_employee_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.team-leader'))
                                    ->relationship('teamLeader', 'name')
                                    ->preload()
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.team-leader-helper-text')),
                                Select::make('partner_id')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.customer'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get): Builder => Project::filterExternalCustomersQuery($query, $get('partner_id')),
                                    )
                                    ->preload()
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.customer-helper-text'))
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('projects::filament/resources/project.form.sections.additional.fields.customer'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(fn (array $data): int => Project::createCustomerPartner($data))
                                    ->editOptionForm(fn (Schema $schema) => PartnerResource::form($schema)),
                                DatePicker::make('start_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.start-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('end_date')
                                    ->beforeOrEqual('end_date'),
                                DatePicker::make('end_date')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.end-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->requiredWith('start_date')
                                    ->afterOrEqual('start_date')
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.end-date-helper-text')),
                                TextInput::make('project_value')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.project-value'))
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.project-value-helper-text'))
                                    // Contract value is financial data (Admin +
                                    // budget-control team only). Hidden fields are
                                    // not dehydrated, so operational roles editing
                                    // a project never see or overwrite the value.
                                    ->visible(fn (): bool => app(\Webkul\Project\Services\OperationalScope::class)->canViewProjectValue())
                                    ->rules(['nullable', 'numeric', 'min:0']),
                                TextInput::make('operational_cost')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.operational-cost'))
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.operational-cost-helper-text'))
                                    ->rules(['nullable', 'numeric', 'min:0']),
                                TextInput::make('allocated_hours')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours'))
                                    ->suffixIcon('heroicon-o-clock')
                                    ->minValue(0)
                                    ->numeric()
                                    ->helperText(__('projects::filament/resources/project.form.sections.additional.fields.allocated-hours-helper-text'))
                                    ->visible(ProjectResource::getTimeSettings()->enable_timesheets)
                                    ->rules(['nullable', 'numeric', 'min:0']),
                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('tags_search'),
                                                                CheckboxList::make('tags')
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name',
                                    modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'tags', 'name'))
                                    
                                    ->columns(2),
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->preload()
                                    ->label(__('projects::filament/resources/project.form.sections.additional.fields.company'))
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, $state) => $set('stage_id', static::getDefaultStageId($state)))
                                    ->createOptionForm(fn (Schema $schema) => CompanyResource::form($schema)),
                            ], $customFormFields))
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('projects::filament/resources/project.form.sections.settings.title'))
                            ->schema([
                                Radio::make('visibility')
                                    ->label(__('projects::filament/resources/project.form.sections.settings.fields.visibility'))
                                    ->default('internal')
                                    ->options(ProjectVisibility::options())
                                    ->descriptions([
                                        'private'  => __('projects::filament/resources/project.form.sections.settings.fields.private-description'),
                                        'internal' => __('projects::filament/resources/project.form.sections.settings.fields.internal-description'),
                                        'public'   => __('projects::filament/resources/project.form.sections.settings.fields.public-description'),
                                    ])
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('projects::filament/resources/project.form.sections.settings.fields.visibility-hint-tooltip')),

                                Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.time-management'))
                                    ->schema([
                                        Toggle::make('allow_timesheets')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-timesheets-helper-text'))
                                            ->visible(ProjectResource::getTimeSettings()->enable_timesheets),
                                    ])
                                    ->columns(1)
                                    ->visible(ProjectResource::getTimeSettings()->enable_timesheets)
                                    ->default(ProjectResource::getTimeSettings()->enable_timesheets),

                                Fieldset::make(__('projects::filament/resources/project.form.sections.settings.fields.task-management'))
                                    ->schema([
                                        Toggle::make('allow_milestones')
                                            ->label(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones'))
                                            ->helperText(__('projects::filament/resources/project.form.sections.settings.fields.allow-milestones-helper-text'))
                                            ->visible(ProjectResource::getTaskSettings()->enable_milestones)
                                            ->default(ProjectResource::getTaskSettings()->enable_milestones),
                                    ])
                                    ->columns(1)
                                    ->visible(ProjectResource::getTaskSettings()->enable_milestones),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    private static function getDefaultStageId($companyId): ?int
    {
        $companyId = $companyId ?: current_company_id();

        return ProjectStage::query()
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $companyId))
            ->orderBy('sort')
            ->first()?->id;
    }
}
