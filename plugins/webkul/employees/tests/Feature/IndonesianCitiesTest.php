<?php

use Webkul\Employee\Data\IndonesianCities;
use Webkul\Employee\Filament\Resources\EmployeeResource\Schemas\EmployeeForm;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

it('catalogs indonesian cities with coordinates for the placement map', function (): void {
    $cities = IndonesianCities::all();

    expect($cities)->toHaveCount(511)
        ->and(IndonesianCities::options())->toHaveKey('Kota Bandung')
        ->and(IndonesianCities::options()['Kota Bandung'])->toContain('Jawa Barat')
        ->and(IndonesianCities::coordinates('Kota Bandung'))
        ->toMatchArray([
            'latitude'  => IndonesianCities::find('Kota Bandung')['latitude'],
            'longitude' => IndonesianCities::find('Kota Bandung')['longitude'],
        ]);

    $bandung = IndonesianCities::coordinates('Kota Bandung');

    expect($bandung['latitude'])->toBeGreaterThan(-11)
        ->and($bandung['latitude'])->toBeLessThan(6.5)
        ->and($bandung['longitude'])->toBeGreaterThan(95)
        ->and($bandung['longitude'])->toBeLessThan(141);
});

it('keeps a custom city in the dropdown when it is not in the catalog', function (): void {
    $options = IndonesianCities::options('Site Khusus');

    expect($options)->toHaveKey('Site Khusus');
});

it('labels a work location with its city for the employee form', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    $location = WorkLocation::factory()->create([
        'name' => 'Kantor Collaboration',
        'city' => 'Kota Bandung',
    ]);

    expect($location->placementLabel())->toBe('Kota Bandung — Kantor Collaboration');
});

it('finds or creates a work location from an indonesian city for the placement map', function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    $first = EmployeeForm::findOrCreatePlacementLocation(
        'Kota Bandung',
        Company::query()->value('id'),
    );
    $second = EmployeeForm::findOrCreatePlacementLocation(
        'Kota Bandung',
        $first?->company_id,
    );

    expect($first)->not->toBeNull()
        ->and($first->city)->toBe('Kota Bandung')
        ->and($first->latitude)->not->toBeNull()
        ->and($first->longitude)->not->toBeNull()
        ->and($second?->id)->toBe($first->id);
});
