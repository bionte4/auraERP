<?php

use Webkul\Employee\Filament\Resources\EmployeeResource\Schemas\EmployeeForm;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../Helpers/EmployeeHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

it('lists this company office as work address, not customers or employee cards', function (): void {
    $employee = EmployeeHelper::employee(['name' => 'ABDULLAH HADI']);
    $employee->refresh();

    $company = Company::query()->first();
    expect($company)->not->toBeNull()
        ->and($company->partner_id)->not->toBeNull();

    $customer = Partner::query()->create([
        'name'         => 'PT Telkom Pelanggan',
        'sub_type'     => 'company',
        'account_type' => AccountType::COMPANY,
        'company_id'   => $company->id,
    ]);

    $ids = EmployeeForm::scopeWorkAddressPartners(Partner::query(), null, $company->id)->pluck('id')->all();

    expect($ids)->toContain((int) $company->partner_id)
        ->and($ids)->not->toContain($customer->id)
        ->and($employee->partner_id)->not->toBeNull()
        ->and($ids)->not->toContain($employee->partner_id);
});
