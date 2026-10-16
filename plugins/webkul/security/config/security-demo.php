<?php

return [
    /*
    | When false, demo emails cannot log in (recommended for real production).
    | Default true so existing demo tenants keep working until you flip it.
    */
    'accounts_allowed' => (bool) env('SECURITY_DEMO_ACCOUNTS_ALLOWED', true),

    'emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env(
            'SECURITY_DEMO_ACCOUNT_EMAILS',
            'hrd@opsora.my.id,karyawan@opsora.my.id,accounting@opsora.my.id,admin@example.com'
        ))
    ))),
];
