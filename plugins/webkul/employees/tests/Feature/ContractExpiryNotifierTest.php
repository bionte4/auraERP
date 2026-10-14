<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmploymentType;
use Webkul\Employee\Services\ContractExpiryNotifier;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    if (! Schema::hasColumn('employees_employees', 'contract_end_date')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/employees/database/migrations/2026_10_14_180000_add_contract_dates_and_employment_type_to_employees_table.php',
        ]);
    }
});

it('seeds default employment types including permanent, contract, konsultan, and freelance', function () {
    $names = EmploymentType::query()->pluck('name')->map(fn (string $name): string => strtolower($name))->all();

    expect($names)
        ->toContain('permanent')
        ->toContain('contract')
        ->toContain('konsultan')
        ->toContain('freelance');
});

it('flags contracts expiring within 30 days and sends database notifications once per contract end date', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-14 09:00:00'));

    try {
        $hrUser = FilamentHelper::actingAs([
            'view_any_employee_employee',
            'update_employee_employee',
        ]);

        $contractType = EmploymentType::query()->where('name', 'Contract')->first();

        $expiringEmployee = Employee::withoutGlobalScopes()->create([
            'name'               => 'Karyawan Kontrak Segera Habis',
            'is_active'          => true,
            'employment_type_id' => $contractType?->id,
            'join_date'          => '2026-01-01',
            'contract_end_date'  => '2026-11-05',
            'company_id'         => $hrUser->default_company_id,
        ]);

        $safeEmployee = Employee::withoutGlobalScopes()->create([
            'name'               => 'Karyawan Kontrak Masih Lama',
            'is_active'          => true,
            'employment_type_id' => $contractType?->id,
            'join_date'          => '2026-01-01',
            'contract_end_date'  => '2027-03-01',
            'company_id'         => $hrUser->default_company_id,
        ]);

        expect($expiringEmployee->daysUntilContractEnds())->toBe(22)
            ->and($expiringEmployee->isContractExpiringSoon())->toBeTrue()
            ->and($safeEmployee->isContractExpiringSoon())->toBeFalse();

        $notifier = app(ContractExpiryNotifier::class);

        $firstRun = $notifier->notifyExpiringContracts();
        $secondRun = $notifier->notifyExpiringContracts();

        expect($firstRun)->toBeGreaterThanOrEqual(1)
            ->and($secondRun)->toBe(0);

        $matchingNotification = $hrUser->notifications()
            ->get()
            ->first(fn ($n): bool => ($n->data['contract_expiry_employee_id'] ?? null) === $expiringEmployee->id);

        expect($matchingNotification)->not->toBeNull();
    } finally {
        Carbon::setTestNow();
    }
});
