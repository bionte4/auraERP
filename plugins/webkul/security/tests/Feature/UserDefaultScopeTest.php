<?php

use Livewire\Livewire;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('defaults the resource permission of new users to individual (least privilege)', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_user',
        'view_security_user',
        'create_security_user',
    ]);

    Livewire::test(CreateUser::class)
        ->assertSet('data.resource_permission', PermissionType::INDIVIDUAL->value);
});
