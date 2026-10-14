<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Models\EvaluationRating;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('evaluations');
});

it('calculates a weighted score on a scale of 1 to 5', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 60,
        'score'         => 4,
        'sort'          => 1,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 40,
        'score'         => 5,
        'sort'          => 2,
    ]);

    expect($evaluation->fresh()->finalScore())->toEqual(4.4);
});

it('returns no score when every criterion has zero weight', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi Nol',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 0,
        'score'         => 5,
    ]);

    expect($evaluation->fresh()->finalScore())->toBeNull();
});

it('ignores criteria that have not been scored', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi Kosong',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 50,
        'score'         => null,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 50,
        'score'         => 5,
    ]);

    expect($evaluation->fresh()->finalScore())->toEqual(5.0);
});
