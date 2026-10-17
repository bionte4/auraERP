<?php

namespace Webkul\Support\Filament\Support;

use Illuminate\Support\Arr;

final class SelectedFilterIds
{
    /**
     * Normalize native/single or legacy multi-select filter values for whereIn().
     *
     * @return array<int, int|string>
     */
    public static function make(mixed $value): array
    {
        return array_values(array_filter(
            Arr::wrap($value),
            fn (mixed $item): bool => filled($item),
        ));
    }
}
