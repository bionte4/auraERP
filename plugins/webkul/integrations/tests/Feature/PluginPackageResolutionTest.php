<?php

use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

it('resolves the integrations package metadata for core plugin records', function (): void {
    $packages = Plugin::getAllPluginPackages(includeCore: true);

    expect($packages)->toHaveKey('integrations')
        ->and($packages['integrations'])->toBeInstanceOf(Package::class)
        ->and($packages['integrations']->isCore)->toBeTrue()
        ->and($packages['integrations']->name)->toBe('integrations');

    $record = Plugin::query()->firstOrCreate(
        ['name' => 'integrations'],
        [
            'author'         => 'auraERP',
            'summary'        => 'SMTP, auraAI, and Jira integrations',
            'description'    => 'SMTP, auraAI, and Jira connection settings with test buttons',
            'latest_version' => '1.0.0',
            'license'        => 'MIT',
            'is_active'      => true,
            'is_installed'   => true,
        ]
    );

    expect($record->package)->toBeInstanceOf(Package::class)
        ->and($record->isCorePackage())->toBeTrue();
});

it('excludes core packages from the installable plugin sync catalog by default', function (): void {
    $packages = Plugin::getAllPluginPackages();

    expect($packages)->not->toHaveKey('integrations')
        ->and($packages)->not->toHaveKey('support')
        ->and($packages)->not->toHaveKey('security');
});
