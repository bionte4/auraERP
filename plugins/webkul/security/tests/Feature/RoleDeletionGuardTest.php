<?php

use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('blocks deleting baseline web roles but allows unused duplicates', function (): void {
    $staffWeb = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $staffSanctum = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'sanctum']);


    expect(RoleResource::getDeletionBlockNotification($staffWeb))->not->toBeNull()
        ->and(RoleResource::getDeletionBlockNotification($staffSanctum))->toBeNull();
});

it('can delete roles on the sanctum guard', function (): void {
    // Regression: Spatie's deleting listener resolves the user model per
    // guard; without an auth "sanctum" guard this crashed with
    // "Class name must be a valid object or a string".
    $role = Role::query()->firstOrCreate(['name' => 'Legacy Sanctum Role', 'guard_name' => 'sanctum']);

    $role->delete();

    expect(Role::query()->whereKey($role->id)->exists())->toBeFalse();
});

it('blocks deleting roles that still have users assigned', function (): void {
    $role = Role::query()->firstOrCreate(['name' => 'Project Coordinator', 'guard_name' => 'web']);

    expect(RoleResource::getDeletionBlockNotification($role))->toBeNull();

    $user = User::factory()->create();
    $user->assignRole($role);

    $block = RoleResource::getDeletionBlockNotification($role);

    expect($block)->not->toBeNull()
        ->and($block['body'])->toContain('1');
});
