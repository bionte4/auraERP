<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\CreateEvaluation;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\ListEvaluations;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('evaluations');

    $pkwtMigration = include __DIR__.'/../../../database/migrations/2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables.php';
    $pkwtMigration->up();

    URL::resolveMissingNamedRoutesUsing(fn () => '/');

    $panel = Filament::getPanel('admin');

    Route::name('filament.'.$panel->getId().'.')->group(function () use ($panel): void {
        EvaluationResource::registerRoutes($panel);
    });
});

it('shows the evaluation list and create pages', function () {
    FilamentHelper::actingAs([
        'view_any_evaluation_evaluation',
        'view_evaluation_evaluation',
        'create_evaluation_evaluation',
    ]);

    Livewire::test(ListEvaluations::class)->assertOk();

    Livewire::test(CreateEvaluation::class)->assertOk();
});
