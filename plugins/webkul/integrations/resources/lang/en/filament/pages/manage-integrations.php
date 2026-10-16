<?php

return [
    'group'      => 'General',
    'breadcrumb' => 'Integrations',
    'title'      => 'Integrations',
    'navigation' => [
        'label' => 'Integrations',
    ],

    'actions' => [
        'test' => 'Test connection',
    ],

    'notifications' => [
        'success' => 'Connection succeeded',
        'failed'  => 'Connection failed',
    ],

    'sections' => [
        'smtp' => [
            'title'       => 'Corporate SMTP',
            'description' => 'Send approval and system mail through your company mailbox. Recipients may use corporate or Gmail addresses.',
        ],
        'ai' => [
            'title'       => 'auraAI',
            'description' => 'API key and model used by the auraAI assistant. Leave disabled to keep answers on the built-in ERP context only.',
        ],
        'jira' => [
            'title'       => 'Jira',
            'description' => 'Authenticate against Jira Cloud or Data Center. Ticket create/sync can follow once this connection stays green.',
        ],
    ],

    'fields' => [
        'enabled'                => 'Enabled',
        'smtp_host'              => 'SMTP host',
        'smtp_port'              => 'Port',
        'smtp_encryption'        => 'Encryption',
        'smtp_encryption_none'   => 'None',
        'smtp_username'          => 'Username',
        'smtp_password'          => 'Password',
        'smtp_from_address'      => 'From address',
        'smtp_from_name'         => 'From name',
        'ai_api_key'             => 'API key',
        'ai_endpoint'            => 'Chat completions endpoint',
        'ai_model'               => 'Model',
        'jira_base_url'          => 'Jira base URL',
        'jira_email'             => 'Account email',
        'jira_api_token'         => 'API token',
        'secret_helper'          => 'Leave blank to keep the saved secret.',
        'last_status'            => 'Last test status',
        'last_tested_at'         => 'Last tested at',
        'last_message'           => 'Last test message',
    ],

    'testers' => [
        'smtp' => [
            'disabled'     => 'Enable SMTP before testing.',
            'incomplete'   => 'Host, from address, and password are required.',
            'no-recipient' => 'Sign in with a user that has an email address to receive the test message.',
            'subject'      => 'auraERP SMTP connection test',
            'body'         => 'This is a test message from auraERP Integrations. The SMTP connection works.',
            'success'      => 'Test mail accepted for delivery to :email.',
            'failed'       => 'SMTP rejected the connection: :error',
        ],
        'ai' => [
            'disabled'   => 'Enable auraAI before testing.',
            'incomplete' => 'API key and endpoint are required.',
            'empty'      => 'The provider responded without a message body.',
            'success'    => 'auraAI answered successfully using model :model.',
            'failed'     => 'auraAI connection failed: :error',
        ],
        'jira' => [
            'disabled'   => 'Enable Jira before testing.',
            'incomplete' => 'Base URL, email, and API token are required.',
            'success'    => 'Authenticated to Jira as :account.',
            'failed'     => 'Jira connection failed: :error',
        ],
    ],
];
