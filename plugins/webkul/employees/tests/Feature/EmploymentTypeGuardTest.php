<?php

use Illuminate\Validation\ValidationException;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmploymentType;
use Webkul\Employee\Services\EmployeeBulkImportService;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('rejects duplicate employment type names ignoring case and spacing', function (): void {
    EmploymentType::query()->create(['name' => 'Contract-HC', 'code' => 'Contract-HC']);

    expect(fn () => EmploymentType::query()->create(['name' => 'contract-hc']))
        ->toThrow(ValidationException::class)
        ->and(fn () => EmploymentType::query()->create(['name' => '  Contract-HC  ']))
        ->toThrow(ValidationException::class);

    expect(EmploymentType::query()->whereRaw('LOWER(name) = ?', ['contract-hc'])->count())->toBe(1);
});

it('allows renaming a type to itself but not to another existing name', function (): void {
    $type = EmploymentType::query()->create(['name' => 'Outsource', 'code' => 'Outsource']);
    EmploymentType::query()->create(['name' => 'Outtask', 'code' => 'Outtask']);

    $type->update(['name' => 'Outsource']);

    expect($type->fresh()->name)->toBe('Outsource');

    expect(fn () => $type->update(['name' => 'outtask']))
        ->toThrow(ValidationException::class);
});

it('imports employees with unknown employment types without creating new types', function (): void {
    EmploymentType::query()->firstOrCreate(['name' => 'Contract'], ['code' => 'Contract']);
    $typesBefore = EmploymentType::query()->count();

    $service = app(EmployeeBulkImportService::class);

    $result = $service->importRows([
        ['name', 'work_email', 'employment_type'],
        ['Known Type', 'known-type@example.com', 'contract'],
        ['Unknown Type', 'unknown-type@example.com', 'Super Kontrak'],
    ]);

    expect($result['created'])->toBe(2)
        ->and(EmploymentType::query()->count())->toBe($typesBefore)
        ->and($service->unknownEmploymentTypes())->toBe(['Super Kontrak']);

    $known = Employee::query()->where('work_email', 'known-type@example.com')->firstOrFail();
    $unknown = Employee::query()->where('work_email', 'unknown-type@example.com')->firstOrFail();

    expect($known->employment_type_id)->not->toBeNull()
        ->and($unknown->employment_type_id)->toBeNull();
});
