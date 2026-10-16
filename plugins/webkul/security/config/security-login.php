<?php

return [
    'enabled' => (bool) env('SECURITY_LOGIN_PROTECTION_ENABLED', true),

    /*
    | Max login attempts per IP+email within the rate-limit window (Filament/API).
    */
    'max_attempts' => (int) env('SECURITY_LOGIN_MAX_ATTEMPTS', 5),

    /*
    | Rate-limit window in minutes.
    */
    'decay_minutes' => (int) env('SECURITY_LOGIN_DECAY_MINUTES', 1),

    /*
    | After this many failed attempts from one IP (within failure window), block the IP.
    */
    'block_after_failures' => (int) env('SECURITY_LOGIN_BLOCK_AFTER_FAILURES', 10),

    /*
    | Window used to count consecutive failures before IP block (minutes).
    */
    'failure_window_minutes' => (int) env('SECURITY_LOGIN_FAILURE_WINDOW_MINUTES', 15),

    /*
    | How long an IP stays blocked (minutes).
    */
    'block_minutes' => (int) env('SECURITY_LOGIN_BLOCK_MINUTES', 30),

    /*
    | Comma-separated IPs that are never blocked (e.g. office VPN).
    */
    'whitelist' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SECURITY_LOGIN_IP_WHITELIST', '127.0.0.1,::1'))
    ))),
];
