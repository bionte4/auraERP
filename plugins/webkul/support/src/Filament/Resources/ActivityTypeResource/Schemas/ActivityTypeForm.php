<?php

namespace Webkul\Support\Filament\Resources\ActivityTypeResource\Schemas;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\ActivityChainingType;
use Webkul\Support\Enums\ActivityDecorationType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Enums\ActivityTypeAction;

class ActivityTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.activity-type-details.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/activity-type.form.sections.activity-type-details.fields.name-tooltip')),
                                        Select::make('category')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.action'))
                                            ->options(ActivityTypeAction::options())
                                            ->live()
                                            ->preload(),
                                        Select::make('default_user_id')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.default-user'))
                                            ->options(fn () => User::query()->pluck('name', 'id'))
                                            ->preload(),
                                        Textarea::make('summary')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.summary'))
                                            ->columnSpanFull(),
                                        RichEditor::make('default_note')
                                            ->label(__('support::filament/resources/activity-type.form.sections.activity-type-details.fields.note'))
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/activity-type.form.sections.delay-information.title'))
                                    ->schema([
                                        TextInput::make('delay_count')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-count'))
                                            ->numeric()
                                            ->required()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(999999999),
                                        Select::make('delay_unit')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-unit'))
                                            ->required()
                                            ->default(ActivityDelayUnit::MINUTES->value)
                                            ->options(ActivityDelayUnit::options()),
                                        Select::make('delay_from')
                                            ->label(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form'))
                                            ->required()
                                            ->default(ActivityDelayFrom::PREVIOUS_ACTIVITY->value)
                                            ->options(ActivityDelayFrom::options())
                                            ->helperText(__('support::filament/resources/activity-type.form.sections.delay-information.fields.delay-form-helper-text')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/activity-type.form.sections.advanced-information.title'))
                                    ->schema([
                                        IconPicker::make('icon')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.icon'))
                                            ->sets(['heroicons', 'fontawesome-solid'])
                                            ->columns(4)
                                            ->gridSearchResults()
                                            ->iconsSearchResults(),
                                        Select::make('decoration_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.decoration-type'))
                                            ->options(ActivityDecorationType::options())
                                            ,
                                        Select::make('chaining_type')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.chaining-type'))
                                            ->options(ActivityChainingType::options())
                                            ->default(ActivityChainingType::SUGGEST->value)
                                            ->live()
                                            ->required()
                                            ->hidden(fn (Get $get) => $get('category') === 'upload_file'),
                                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('activity_type_suggestions_search'),
                                                                                CheckboxList::make('activity_type_suggestions')
                                            ->relationship('suggestedActivityTypes', 'name',
                                    fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'activity_type_suggestions', 'name'))
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.suggest'))
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'trigger' || $get('category') === 'upload_file')
                                            
                                            ->columns(2),
                                        Select::make('triggered_next_type_id')
                                            ->relationship('triggeredNextType', 'name')
                                            ->label(__('support::filament/resources/activity-type.form.sections.advanced-information.fields.trigger'))
                                            ->hidden(fn (Get $get) => $get('chaining_type') === 'suggest' && $get('category') !== 'upload_file'),
                                    ]),
                                Section::make(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.title'))
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.status'))
                                            ->default(false),
                                        Toggle::make('keep_done')
                                            ->label(__('support::filament/resources/activity-type.form.sections.status-and-configuration-information.fields.keep-done-activities'))
                                            ->default(false),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
