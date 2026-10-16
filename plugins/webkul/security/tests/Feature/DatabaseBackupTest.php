<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Pages\ManageDatabaseBackups;
use Webkul\Security\Models\User;
use Webkul\Security\Services\DatabaseBackupManager;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();

    $this->backupDir = storage_path('framework/testing/database-backups-'.uniqid());
    File::ensureDirectoryExists($this->backupDir);

    config([
        'security-backup.enabled'         => true,
        'security-backup.path'            => $this->backupDir,
        'security-backup.prefer_native'   => false,
        'security-backup.compress'        => false,
        'security-backup.retention_days'  => 30,
        'security-backup.retention_count' => 10,
    ]);
});

afterEach(function (): void {
    SecurityHelper::restoreUserEvents();

    if (isset($this->backupDir) && File::isDirectory($this->backupDir)) {
        File::deleteDirectory($this->backupDir);
    }
});

it('allows only admin to access the database backups page', function (): void {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => $adminRoleName, 'guard_name' => $guard]);
        Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => $guard]);
    }

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    expect(ManageDatabaseBackups::canAccess())->toBeTrue()
        ->and(ManageDatabaseBackups::shouldRegisterNavigation())->toBeTrue();

    Livewire::test(ManageDatabaseBackups::class)
        ->assertOk()
        ->assertSee(__('security::filament/pages/manage-database-backups.info.heading'));

    $staff = User::withoutEvents(fn () => User::factory()->create());
    $staff->syncRoles(['Staff']);

    Auth::guard('web')->login($staff);
    Auth::guard('sanctum')->setUser($staff);
    Auth::shouldUse('sanctum');

    expect(ManageDatabaseBackups::canAccess())->toBeFalse()
        ->and(ManageDatabaseBackups::shouldRegisterNavigation())->toBeFalse();
});

it('creates lists and deletes a database backup file', function (): void {
    if (! Schema::hasTable('users')) {
        $this->markTestSkipped('users table is required for PHP SQL dump.');
    }

    $manager = app(DatabaseBackupManager::class);
    $meta = $manager->create('test-backup');

    expect(File::exists($meta['path']))->toBeTrue()
        ->and($manager->list())->toHaveCount(1)
        ->and($manager->list()->first()['filename'])->toBe($meta['filename']);

    $manager->delete($meta['filename']);

    expect($manager->list())->toHaveCount(0)
        ->and(File::exists($meta['path']))->toBeFalse();
});

it('prunes backups beyond retention count', function (): void {
    if (! Schema::hasTable('users')) {
        $this->markTestSkipped('users table is required for PHP SQL dump.');
    }

    config(['security-backup.retention_count' => 1, 'security-backup.retention_days' => 0]);

    $manager = app(DatabaseBackupManager::class);
    $first = $manager->create('first');
    sleep(1);
    $second = $manager->create('second');

    expect($manager->list()->count())->toBe(2);

    $deleted = $manager->prune();

    expect($deleted)->toBeGreaterThanOrEqual(1)
        ->and($manager->list()->count())->toBe(1)
        ->and($manager->list()->first()['filename'])->toBe($second['filename'])
        ->and(File::exists($first['path']))->toBeFalse();
});
