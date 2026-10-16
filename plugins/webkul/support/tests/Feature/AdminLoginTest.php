<?php

use Filament\Auth\Pages\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Webkul\Security\Models\User;

it('authenticates the local admin account through the filament login page', function () {
    RateLimiter::clear(strtolower('admin@example.com').'|127.0.0.1');

    DB::table('users')->where('email', 'admin@example.com')->update([
        'password'                          => Hash::make('password'),
        'is_active'                         => true,
        'deleted_at'                        => null,
        'email_verified_at'                 => now(),
        'has_email_authentication'          => false,
        'app_authentication_secret'         => null,
        'app_authentication_recovery_codes' => null,
    ]);

    $user = User::query()->where('email', 'admin@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();

    Livewire::test(Login::class)
        ->fillForm([
            'email'    => 'admin@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect();
});
