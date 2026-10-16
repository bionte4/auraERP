<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Resources\Resource;
use Webkul\Support\Models\State;

class StateResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $model = State::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';
}
