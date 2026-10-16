<?php

namespace Webkul\Security\Support;

use Webkul\Security\Models\User;

final class DemoAccountGuard
{
    public static function shouldBlock(?User $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        if (config('security-demo.accounts_allowed', true)) {
            return false;
        }

        $emails = collect(config('security-demo.emails', []))
            ->map(fn ($email): string => mb_strtolower(trim((string) $email)))
            ->filter()
            ->all();

        return in_array(mb_strtolower((string) $user->email), $emails, true);
    }
}
