<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Webkul\Security\Services\DatabaseBackupManager;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();

    $this->backupDir = storage_path('framework/testing/restore-guard-'.uniqid());
    File::ensureDirectoryExists($this->backupDir);

    config([
        'security-backup.path'          => $this->backupDir,
        'security-backup.prefer_native' => false,
        'security-backup.compress'      => false,
        'security-backup.allow_restore' => null,
    ]);

    app()->instance('env', 'production');
});

afterEach(function (): void {
    SecurityHelper::restoreUserEvents();

    if (isset($this->backupDir) && File::isDirectory($this->backupDir)) {
        File::deleteDirectory($this->backupDir);
    }
});

it('disables restore by default in production', function (): void {
    app()->instance('env', 'production');
    config(['security-backup.allow_restore' => null]);

    $manager = app(DatabaseBackupManager::class);

    expect($manager->restoreAllowed())->toBeFalse();

    if (! Schema::hasTable('users')) {
        $this->markTestSkipped('users table required');
    }

    $meta = $manager->create('guard-test');

    expect(fn () => $manager->restore($meta['filename']))
        ->toThrow(RuntimeException::class);
});

it('allows restore when explicitly enabled', function (): void {
    app()->instance('env', 'production');
    config(['security-backup.allow_restore' => true]);

    expect(app(DatabaseBackupManager::class)->restoreAllowed())->toBeTrue();
});
