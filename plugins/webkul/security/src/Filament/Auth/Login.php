<?php

namespace Webkul\Security\Filament\Auth;

use App\Http\Responses\FilamentLoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Timebox;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Webkul\Security\Services\LoginProtection;

class Login extends BaseLogin
{
    public function mount(): void
    {
        if (Filament::auth()->check()) {
            FilamentLoginResponse::forgetUnsafeIntendedUrl();

            $this->redirect(FilamentLoginResponse::homeUrl());

            return;
        }

        $this->form->fill();
    }

    public function getHeading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return __('security::auth.login.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return __('security::auth.login.subheading');
    }

    public function authenticate(): ?LoginResponse
    {
        $protection = app(LoginProtection::class);
        $email = is_string($this->data['email'] ?? null) ? $this->data['email'] : null;

        try {
            $protection->assertNotBlocked();
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'data.email' => $exception->getMessage(),
            ]);
        }

        if ($protection->tooManyAttempts($email)) {
            $seconds = max(1, $protection->availableIn($email));

            $this->getRateLimitedNotification(new TooManyRequestsException(
                static::class,
                'authenticate',
                request()->ip() ?: '0.0.0.0',
                $seconds,
            ))?->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $authProvider = $authGuard->getProvider(); /** @phpstan-ignore-line */
        $credentials = $this->getCredentialsFromFormData($data);
        $remember = $data['remember'] ?? false;
        $timeboxDuration = (int) config('auth.timebox_duration', 200_000);

        $user = app(Timebox::class)->call(function (Timebox $timebox) use ($authProvider, $authGuard, $credentials, $remember): Authenticatable {
            $this->fireAttemptingEvent($authGuard, $credentials, $remember);

            $user = $authProvider->retrieveByCredentials($credentials);

            if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
                $this->userUndertakingMultiFactorAuthentication = null;

                $this->fireFailedEvent($authGuard, $user, $credentials);
                $this->throwFailureValidationException();
            }

            if (! $this->isUserAllowedToAccessPanel($user)) {
                $this->userUndertakingMultiFactorAuthentication = null;

                $this->fireFailedEvent($authGuard, $user, $credentials);
                $this->throwFailureValidationException();
            }

            $timebox->returnEarly();

            return $user;
        }, $timeboxDuration);

        $needsMultiFactorChallenge = app(Timebox::class)->call(function (Timebox $timebox) use ($user): bool {
            if (
                filled($this->userUndertakingMultiFactorAuthentication) &&
                (decrypt($this->userUndertakingMultiFactorAuthentication) === $user->getAuthIdentifier())
            ) {
                if ($this->isMultiFactorChallengeRateLimited($user)) {
                    return true;
                }

                $this->multiFactorChallengeForm->validate();

                return false;
            }

            $multiFactorChallenge = $this->getMultiFactorChallenge();

            if ($multiFactorAuthenticationProvider = $multiFactorChallenge->getFirstEnabledProvider($user)) {
                $this->userUndertakingMultiFactorAuthentication = encrypt($user->getAuthIdentifier());

                $multiFactorChallenge->beforeChallenge($user, $multiFactorAuthenticationProvider);
            }

            if (filled($this->userUndertakingMultiFactorAuthentication)) {
                $this->multiFactorChallengeForm->fill();

                return true;
            }

            if (Filament::getMultiFactorAuthenticationProviders() === []) {
                $timebox->returnEarly();
            }

            return false;
        }, $timeboxDuration);

        if ($needsMultiFactorChallenge) {
            return null;
        }

        if (! $authGuard->attemptWhen($credentials, fn (Authenticatable $user): bool => $this->isUserAllowedToAccessPanel($user), $remember)) {
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
