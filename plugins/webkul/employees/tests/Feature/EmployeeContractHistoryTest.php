<?php

use Illuminate\Support\Facades\Artisan;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageContracts;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeContract;
use Webkul\Employee\Models\EmploymentType;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('numbers contracts sequentially and supersedes the previous active contract', function (): void {
    $employee = Employee::factory()->create(['user_id' => null]);

    $first = EmployeeContract::query()->create([
        'employee_id' => $employee->id,
        'start_date'  => '2024-01-01',
        'end_date'    => '2024-12-31',
        'status'      => EmployeeContract::STATUS_ACTIVE,
    ]);

    $second = EmployeeContract::query()->create([
        'employee_id' => $employee->id,
        'start_date'  => '2025-01-01',
        'end_date'    => '2025-12-31',
        'status'      => EmployeeContract::STATUS_ACTIVE,
    ]);

    expect($first->sequence)->toBe(1)
        ->and($second->sequence)->toBe(2)
        ->and($first->fresh()->status)->toBe(EmployeeContract::STATUS_EXTENDED)
        ->and($employee->fresh()->contract_end_date?->format('Y-m-d'))->toBe('2025-12-31')
        ->and($employee->currentContract()?->is($second))->toBeTrue();
});

it('calculates cumulative PKWT months against the 60 month cap', function (): void {
    $pkwt = EmploymentType::query()->firstOrCreate(['name' => 'PKWT'], ['code' => 'PKWT']);
    $permanent = EmploymentType::query()->firstOrCreate(['name' => 'Permanent'], ['code' => 'Permanent']);

    $employee = Employee::factory()->create(['user_id' => null]);

    EmployeeContract::query()->create([
        'employee_id'        => $employee->id,
        'employment_type_id' => $pkwt->id,
        'start_date'         => '2023-01-01',
        'end_date'           => '2023-12-31',
        'status'             => EmployeeContract::STATUS_COMPLETED,
    ]);
    EmployeeContract::query()->create([
        'employee_id'        => $employee->id,
        'employment_type_id' => $pkwt->id,
        'start_date'         => '2024-01-01',
        'end_date'           => '2024-12-31',
        'status'             => EmployeeContract::STATUS_ACTIVE,
    ]);
    EmployeeContract::query()->create([
        'employee_id'        => $employee->id,
        'employment_type_id' => $permanent->id,
        'start_date'         => '2025-01-01',
        'end_date'           => null,
        'status'             => EmployeeContract::STATUS_CONVERTED,
    ]);

    // 11 + 11 whole months of PKWT; the converted permanent contract is excluded.
    expect($employee->cumulativePkwtMonths())->toBe(22);
});

it('backfills an initial contract from join and contract end dates', function (): void {
    $employee = Employee::factory()->create([
        'user_id'           => null,
        'join_date'         => '2024-03-01',
        'contract_end_date' => '2027-02-28',
    ]);

    Artisan::call('employees:backfill-contracts');

    $contract = $employee->contracts()->first();

    expect($contract)->not->toBeNull()
        ->and($contract->sequence)->toBe(1)
        ->and($contract->source)->toBe('backfill')
        ->and($contract->status)->toBe(EmployeeContract::STATUS_ACTIVE)
        ->and($contract->start_date->format('Y-m-d'))->toBe('2024-03-01');

    // Idempotent: a second run must not duplicate the history.
    Artisan::call('employees:backfill-contracts');

    expect($employee->contracts()->count())->toBe(1);
});

it('renders the contract history tab', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
        'view_employee_employee',
    ]);

    $employee = Employee::factory()->create(['name' => 'Contracted Person', 'user_id' => null, 'creator_id' => $user->id]);

    EmployeeContract::query()->create([
        'employee_id' => $employee->id,
        'start_date'  => '2025-01-01',
        'end_date'    => '2025-12-31',
        'status'      => EmployeeContract::STATUS_ACTIVE,
    ]);

    \Livewire\Livewire::test(ManageContracts::class, ['record' => $employee->id])
        ->assertOk()
        ->assertSee('Contracted Person');
});
