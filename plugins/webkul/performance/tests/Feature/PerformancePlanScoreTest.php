<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Performance\Models\PerformanceGoal;
use Webkul\Performance\Models\PerformancePlan;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('performance');
});

it('calculates a weighted score from goal progress', function () {
    $employee = Employee::withoutGlobalScopes()->first()
        ?? Employee::withoutGlobalScopes()->create([
            'name'      => 'Uji Kinerja',
            'is_active' => true,
        ]);

    expect($employee)->not->toBeNull();

    $plan = PerformancePlan::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    PerformanceGoal::factory()->create([
        'performance_plan_id' => $plan->id,
        'weight'              => 60,
        'progress'            => 50,
        'sort'                => 1,
    ]);

    PerformanceGoal::factory()->create([
        'performance_plan_id' => $plan->id,
        'weight'              => 40,
        'progress'            => 100,
        'sort'                => 2,
    ]);

    expect($plan->fresh()->weightedScore())->toEqual(70.0);
});

it('returns no score when every goal has zero weight', function () {
    $employee = Employee::withoutGlobalScopes()->first()
        ?? Employee::withoutGlobalScopes()->create([
            'name'      => 'Uji Kinerja',
            'is_active' => true,
        ]);

    $plan = PerformancePlan::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    PerformanceGoal::factory()->create([
        'performance_plan_id' => $plan->id,
        'weight'              => 0,
        'progress'            => 80,
    ]);

    expect($plan->fresh()->weightedScore())->toBeNull();
});
