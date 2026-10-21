<?php

namespace Webkul\Support\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * CheckboxList multi-select with Livewire search (no Alpine x-load).
 *
 * Filament's CheckboxList::searchable() filters client-side via Alpine
 * checkbox-list.js. When that asset fails to init, the search box still
 * accepts input but options never filter. This helper filters on the server.
 */
class LivewireSearchableCheckboxList
{
    /**
     * Relationship-backed list.
     *
     * @param  Closure(CheckboxList): (CheckboxList|null)|null  $configure
     * @return array{0: TextInput, 1: CheckboxList}
     */
    public static function make(
        string $name,
        string $relationship,
        string $titleAttribute = 'name',
        ?string $label = null,
        ?string $helperText = null,
        ?string $searchPlaceholder = null,
        int $columns = 2,
        ?Closure $modifyQueryUsing = null,
        ?Closure $configure = null,
    ): array {
        $searchField = self::searchFieldName($name);

        $search = self::searchInput($searchField, $searchPlaceholder);

        $list = CheckboxList::make($name)
            ->label($label)
            ->helperText($helperText)
            ->relationship(
                name: $relationship,
                titleAttribute: $titleAttribute,
                modifyQueryUsing: self::relationshipSearchModifier(
                    $name,
                    $searchField,
                    $titleAttribute,
                    $modifyQueryUsing,
                ),
            )
            ->columns($columns)
            ->columnSpanFull();

        if ($configure) {
            $list = $configure($list) ?? $list;
        }

        return [$search, $list];
    }

    /**
     * Options-backed list (array or closure).
     *
     * @param  array<string|int, string>|Closure  $options
     * @param  Closure(CheckboxList): (CheckboxList|null)|null  $configure
     * @return array{0: TextInput, 1: CheckboxList}
     */
    public static function fromOptions(
        string $name,
        array|Closure $options,
        ?string $label = null,
        ?string $helperText = null,
        ?string $searchPlaceholder = null,
        int $columns = 2,
        ?Closure $configure = null,
    ): array {
        $searchField = self::searchFieldName($name);

        $search = self::searchInput($searchField, $searchPlaceholder);

        $list = CheckboxList::make($name)
            ->label($label)
            ->helperText($helperText)
            ->options(function (Get $get) use ($name, $searchField, $options): array {
                $all = $options instanceof Closure ? (array) $options($get) : $options;

                return self::filterOptions($all, $get, $name, $searchField);
            })
            ->columns($columns)
            ->columnSpanFull();

        if ($configure) {
            $list = $configure($list) ?? $list;
        }

        return [$search, $list];
    }

    public static function searchFieldName(string $name): string
    {
        return "{$name}_search";
    }

    /**
     * Apply Livewire search to a relationship query (for inline modifyQueryUsing).
     */
    public static function applySearch(
        Builder $query,
        Get $get,
        string $name,
        string $titleAttribute = 'name',
        ?string $searchField = null,
        bool $selectedOnlyWhenUnsearched = false,
    ): Builder {
        $searchField ??= self::searchFieldName($name);
        $search = trim((string) $get($searchField));
        $selected = array_values(array_filter(
            array_map('intval', Arr::wrap($get($name) ?? [])),
            fn (int $id): bool => $id > 0,
        ));

        $query->orderBy($titleAttribute);

        if ($search === '') {
            if ($selectedOnlyWhenUnsearched) {
                if ($selected === []) {
                    return $query->whereRaw('0 = 1');
                }

                return $query->whereIn($query->getModel()->getQualifiedKeyName(), $selected);
            }

            return $query;
        }

        $like = '%'.mb_strtolower($search).'%';
        $model = $query->getModel();
        $key = $model->getQualifiedKeyName();
        $column = $model->qualifyColumn($titleAttribute);

        return $query->where(function (Builder $inner) use ($column, $like, $selected, $key): void {
            $inner->whereRaw('LOWER('.$column.') LIKE ?', [$like]);

            if ($selected !== []) {
                $inner->orWhereIn($key, $selected);
            }
        });
    }

    /**
     * Filter an options array by Livewire search term.
     *
     * @param  array<string|int, string>  $options
     * @return array<string|int, string>
     */
    public static function filterOptions(
        array $options,
        Get $get,
        string $name,
        ?string $searchField = null,
    ): array {
        $searchField ??= self::searchFieldName($name);
        $term = trim((string) $get($searchField));
        $selected = array_map('strval', Arr::wrap($get($name) ?? []));

        if ($term === '') {
            return $options;
        }

        $needle = mb_strtolower($term);

        return array_filter(
            $options,
            function (mixed $label, mixed $id) use ($needle, $selected): bool {
                if (in_array((string) $id, $selected, true)) {
                    return true;
                }

                return str_contains(mb_strtolower((string) $label), $needle);
            },
            ARRAY_FILTER_USE_BOTH,
        );
    }

    /**
     * Build the Livewire search TextInput to place above a CheckboxList.
     */
    public static function searchInput(string $searchField, ?string $searchPlaceholder = null): TextInput
    {
        return TextInput::make($searchField)
            ->label($searchPlaceholder ?? __('Search'))
            ->placeholder($searchPlaceholder ?? __('Search...'))
            ->prefixIcon('heroicon-m-magnifying-glass')
            ->live(debounce: 300)
            ->dehydrated(false)
            ->columnSpanFull();
    }

    /**
     * @return Closure(Builder, Get): Builder
     */
    protected static function relationshipSearchModifier(
        string $name,
        string $searchField,
        string $titleAttribute,
        ?Closure $existingModify,
    ): Closure {
        return function (Builder $query, Get $get) use ($name, $searchField, $titleAttribute, $existingModify): Builder {
            if ($existingModify) {
                $result = $existingModify($query, $get);
                if ($result instanceof Builder) {
                    $query = $result;
                }
            }

            return self::applySearch($query, $get, $name, $titleAttribute, $searchField);
        };
    }
}
