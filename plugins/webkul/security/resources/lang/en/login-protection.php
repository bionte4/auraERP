<?php

return [
    'errors' => [
        'ip-blocked'  => 'Too many failed login attempts from this IP. Try again in about :minutes minute(s).',
        'throttled'   => 'Too many login attempts. Please wait :seconds second(s) and try again.',
    ],

    'audit' => [
        'blocked'   => 'IP :ip blocked for :minutes minute(s)',
        'unblocked' => 'IP :ip unblocked',
    ],
];
