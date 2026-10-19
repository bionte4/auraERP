<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Schemas;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;

class AccountForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.code'))
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->required()
                            ->label(__('accounts::filament/resources/account.form.sections.fields.account-name'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        Fieldset::make(__('accounts::filament/resources/account.form.sections.fields.accounting'))
                            ->schema([
                                Select::make('account_type')
                                    ->options(AccountType::groupedOptions())
                                    ->preload()
                                    ->required()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.account-type'))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $existing = $get('invoices_account_journals');

                                        if (! empty($existing)) {
                                            return;
                                        }

                                        $journalIds = static::suggestJournalIdsForAccountType($state);

                                        if (! empty($journalIds)) {
                                            $set('invoices_account_journals', $journalIds);
                                        }
                                    })
                                    ,
                                Select::make('parent_id')
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query, ?Account $record) {
                                            if ($record) {
                                                $excludedIds = [
                                                    $record->id,
                                                    ...$record->getDescendantIds(),
                                                ];

                                                $query->whereNotIn('id', $excludedIds);
                                            }
                                        },
                                    )
                                    ->getOptionLabelFromRecordUsing(function (Account $record) {
                                        if ($record->code) {
                                            return "{$record->code} - {$record->name}";
                                        }

                                        return $record->name;
                                    })
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.parent-account'))
                                    ->helperText(__('accounts::filament/resources/account.form.sections.fields.parent-account-helper'))
                                    ->preload()
                                    ,
                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('invoices_account_tax_search'),
                                                                CheckboxList::make('invoices_account_tax')
                                    ->relationship('taxes', 'name',
                                    fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'invoices_account_tax', 'name'))
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.default-taxes'))
                                    ->hidden(fn (Get $get) => $get('account_type') === AccountType::OFF_BALANCE->value)
                                    
                                    ->columns(2),
                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('invoices_account_account_tags_search'),
                                                                CheckboxList::make('invoices_account_account_tags')
                                    ->relationship('tags', 'name',
                                    fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'invoices_account_account_tags', 'name'))
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.tags'))
                                    
                                    ->columns(2),
                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('invoices_account_journals_search'),
                                                                CheckboxList::make('invoices_account_journals')
                                    ->relationship('journals', 'name',
                                    fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'invoices_account_journals', 'name'))
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.journals'))
                                    ->helperText(__('accounts::filament/resources/account.form.sections.fields.journals-helper'))
                                    
                                    ->columns(2),
                                Select::make('currency_id')
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                    )
                                    ->preload()
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.currency'))
                                    ,
                                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('companies_search'),
                                                                CheckboxList::make('companies')
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.companies'))
                                    ->relationship('companies', 'name',
                                    fn (\Illuminate\Database\Eloquent\Builder $query, \Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::applySearch($query, $get, 'companies', 'name'))
                                    ->required()
                                    
                                    ->columns(2),
                                Toggle::make('deprecated')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.deprecated')),
                                Toggle::make('reconcile')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.reconcile')),
                                Toggle::make('non_trade')
                                    ->inline(false)
                                    ->label(__('accounts::filament/resources/account.form.sections.fields.non-trade')),
                            ]),
                    ]),
                Section::make()
                    ->schema($customFormFields)
                    ->columns(2),
            ])
            ->columns(1);
    }

    private static function suggestJournalIdsForAccountType(?string $accountType): array
    {
        if (! $accountType) {
            return [];
        }

        $journalType = match ($accountType) {
            AccountType::INCOME->value,
            AccountType::INCOME_OTHER->value,
            AccountType::ASSET_RECEIVABLE->value       => JournalType::SALE,
            AccountType::EXPENSE->value,
            AccountType::EXPENSE_DEPRECIATION->value,
            AccountType::EXPENSE_DIRECT_COST->value,
            AccountType::LIABILITY_PAYABLE->value      => JournalType::PURCHASE,
            AccountType::ASSET_CASH->value             => JournalType::CASH,
            AccountType::LIABILITY_CREDIT_CARD->value  => JournalType::CREDIT_CARD,
            default                                    => null,
        };

        if (! $journalType) {
            return [];
        }

        return Journal::query()
            ->where('type', $journalType->value)
            ->pluck('id')
            ->all();
    }
}
