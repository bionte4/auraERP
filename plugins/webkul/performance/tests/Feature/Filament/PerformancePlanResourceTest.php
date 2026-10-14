<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Performance\Filament\Resources\PerformancePlanResource;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\CreatePerformancePlan;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\ListPerformancePlans;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('performance');

    URL::resolveMissingNamedRoutesUsing(fn () => '/');

    $panel = Filament::getPanel('admin');

    Route::name('filament.'.$panel->getId().'.')->group(function () use ($panel): void {
        PerformancePlanResource::registerRoutes($panel);
    });
});

it('shows the performance plan list and create pages', function () {
    FilamentHelper::actingAs([
        'view_any_performance_performance::plan',
        'view_performance_performance::plan',
        'create_performance_performance::plan',
    ]);

    Livewire::test(ListPerformancePlans::class)->assertOk();

    Livewire::test(CreatePerformancePlan::class)->assertOk();
});
