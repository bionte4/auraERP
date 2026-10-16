<?php

return [
    'title' => 'Blocked IPs',

    'navigation' => [
        'label' => 'Blocked IPs',
    ],

    'info' => [
        'heading'    => 'Login rate limit & IP block',
        'enabled'    => 'Protection: :status',
        'on'         => 'enabled',
        'off'        => 'disabled',
        'rate-limit' => 'Rate limit: :attempts failed attempt(s) per email+IP within :minutes minute(s).',
        'ip-block'   => 'IP block after :failures failure(s) in :window minute(s), for :minutes minute(s).',
        'whitelist'  => 'Whitelist (never blocked): :ips',
    ],

    'table' => [
        'ip'            => 'IP address',
        'attempts'      => 'Failed attempts',
        'reason'        => 'Reason',
        'last-email'    => 'Last email',
        'blocked-until' => 'Blocked until',
        'actions'       => 'Actions',
        'empty'         => 'No IPs are currently blocked.',
    ],

    'actions' => [
        'refresh'         => 'Refresh',
        'unblock'         => 'Unblock',
        'unblock-confirm' => 'Unblock this IP address?',
    ],

    'notifications' => [
        'unblocked' => [
            'title' => 'IP unblocked',
            'body'  => 'Address :ip can attempt login again.',
        ],
    ],
];
