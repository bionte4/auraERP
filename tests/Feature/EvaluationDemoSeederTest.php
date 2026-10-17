<?php

require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/SecurityHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Database\Seeders\EvaluationDemoSeeder;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Support\Models\Company;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('evaluations');
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('performance');
    SecurityHelper::disableUserEvents();

    $pkwtMigration = include __DIR__.'/../../plugins/webkul/evaluations/database/migrations/2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables.php';
    $pkwtMigration->up();

    Company::query()->first() ?? Company::factory()->create();
});

it('seeds one understandable demo employee evaluation', function (): void {
    $this->seed(EvaluationDemoSeeder::class);

    $employee = Employee::withoutGlobalScopes()
        ->where('identification_id', EvaluationDemoSeeder::EMPLOYEE_NIK)
        ->first();

    $evaluation = Evaluation::query()
        ->where('employee_id', $employee?->id)
        ->where('evaluation_type', EvaluationType::PkwtQ1)
        ->first();

    expect($employee)->not->toBeNull()
        ->and($employee->name)->toBe('Andi Pratama')
        ->and($evaluation)->not->toBeNull()
        ->and($evaluation->title)->toContain('Andi Pratama')
        ->and($evaluation->ratings()->count())->toBe(10)
        ->and($evaluation->ratings()->whereNotNull('score')->count())->toBe(10);

    // Idempotent: second run does not duplicate the evaluation.
    $this->seed(EvaluationDemoSeeder::class);

    expect(
        Evaluation::query()
            ->where('employee_id', $employee->id)
            ->where('evaluation_type', EvaluationType::PkwtQ1)
            ->count()
    )->toBe(1);
});
