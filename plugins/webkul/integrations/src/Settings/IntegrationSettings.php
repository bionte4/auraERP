<?php

namespace Webkul\Integrations\Settings;

use Spatie\LaravelSettings\Settings;

class IntegrationSettings extends Settings
{
    public bool $smtp_enabled;

    public ?string $smtp_host;

    public int $smtp_port;

    public ?string $smtp_encryption;

    public ?string $smtp_username;

    public ?string $smtp_password;

    public ?string $smtp_from_address;

    public ?string $smtp_from_name;

    public ?string $smtp_last_tested_at;

    public ?string $smtp_last_status;

    public ?string $smtp_last_message;

    public bool $ai_enabled;

    public ?string $ai_api_key;

    public ?string $ai_endpoint;

    public ?string $ai_model;

    public ?string $ai_last_tested_at;

    public ?string $ai_last_status;

    public ?string $ai_last_message;

    public bool $jira_enabled;

    public ?string $jira_base_url;

    public ?string $jira_email;

    public ?string $jira_api_token;

    public ?string $jira_last_tested_at;

    public ?string $jira_last_status;

    public ?string $jira_last_message;

    public static function group(): string
    {
        return 'integrations';
    }
}
