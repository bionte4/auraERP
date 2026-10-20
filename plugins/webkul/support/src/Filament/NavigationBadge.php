<?php

namespace Webkul\Support\Filament;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Shared formatter/counter for sidebar navigation badges.
 *
 * Badges are only for actionable counts: hidden when zero, capped at
 * "99+", and cached briefly because navigation is rendered (and badges
 * evaluated) on every admin page load.
 */
class NavigationBadge
{
    public static function value(string $key, Closure $callback): int
    {
        try {
            return (int) Cache::remember(
                'nav-badge:'.$key.':u'.(auth()->id() ?? 'guest'),
                now()->addSeconds(60),
                $callback,
            );
        } catch (Throwable) {
            return 0;
        }
    }

    public static function count(string $key, Closure $callback): ?string
    {
        $count = static::value($key, $callback);

        if ($count <= 0) {
            return null;
        }

        return $count > 99 ? '99+' : (string) $count;
    }
}
