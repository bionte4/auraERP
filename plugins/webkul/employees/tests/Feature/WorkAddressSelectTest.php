<?php

use Webkul\Employee\Filament\Resources\EmployeeResource\Schemas\EmployeeForm;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../Helpers/EmployeeHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

it('lists company offices as work addresses, not employee contact cards', function (): void {
    $employee = EmployeeHelper::employee(['name' => 'ABDULLAH HADI']);
    $employee->refresh();

    $office = Partner::query()->create([
        'name'         => 'Kantor Collaboration',
        'street1'      => 'Jl. Sudirman 1',
        'city'         => 'Jakarta',
        'sub_type'     => 'company',
        'account_type' => AccountType::COMPANY,
    ]);

    $person = Partner::query()->create([
        'name'         => 'AAN SETIAWAN',
        'sub_type'     => 'partner',
        'account_type' => AccountType::INDIVIDUAL,
    ]);

    $ids = EmployeeForm::scopeWorkAddressPartners(Partner::query())->pluck('id')->all();

    expect($ids)->toContain($office->id)
        ->and($ids)->not->toContain($person->id)
        ->and($employee->partner_id)->not->toBeNull()
        ->and($ids)->not->toContain($employee->partner_id)
        ->and(EmployeeForm::workAddressLabel($office->fresh()))
        ->toBe('Kantor Collaboration — Jl. Sudirman 1 — Jakarta');
});
