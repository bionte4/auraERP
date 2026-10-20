<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Widgets\ExpiringProjectsWidget;
use Webkul\Project\Models\Project;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lists expiring and expired active projects with clickable links, sorted by end date', function (): void {
    $expired = Project::factory()->create([
        'name'      => 'Expired Dashboard Project',
        'is_active' => true,
        'end_date'  => now()->subDays(5)->toDateString(),
    ]);
    $soon = Project::factory()->create([
        'name'      => 'Soon Dashboard Project',
        'is_active' => true,
        'end_date'  => now()->addDays(10)->toDateString(),
    ]);
    Project::factory()->create([
        'name'      => 'Far Future Dashboard Project',
        'is_active' => true,
        'end_date'  => now()->addYear()->toDateString(),
    ]);
    Project::factory()->create([
        'name'      => 'Inactive Soon Project',
        'is_active' => false,
        'end_date'  => now()->addDays(3)->toDateString(),
    ]);

    FilamentHelper::actingAs([
        'widget_project_expiring_projects_widget',
        'view_project_project',
        'view_any_project_project',
    ]);

    Livewire::test(ExpiringProjectsWidget::class)
        ->assertOk()
        ->assertSee('Expired Dashboard Project')
        ->assertSee('Soon Dashboard Project')
        ->assertSeeHtml('/admin/project/projects/'.$expired->getRouteKey())
        ->assertSeeHtml('/admin/project/projects/'.$soon->getRouteKey())
        ->assertDontSee('Far Future Dashboard Project')
        ->assertDontSee('Inactive Soon Project');
});

it('grants expiring projects widget permission to admin and operation manager only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'widget_project_expiring_projects_widget';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();
    }

    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();
    expect($staffRole->hasPermissionTo($permission))->toBeFalse();
});
