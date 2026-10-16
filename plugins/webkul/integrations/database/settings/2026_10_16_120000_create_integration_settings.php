<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('integrations.smtp_enabled', false);
        $this->migrator->add('integrations.smtp_host', null);
        $this->migrator->add('integrations.smtp_port', 587);
        $this->migrator->add('integrations.smtp_encryption', 'tls');
        $this->migrator->add('integrations.smtp_username', null);
        $this->migrator->add('integrations.smtp_password', null);
        $this->migrator->add('integrations.smtp_from_address', null);
        $this->migrator->add('integrations.smtp_from_name', null);
        $this->migrator->add('integrations.smtp_last_tested_at', null);
        $this->migrator->add('integrations.smtp_last_status', null);
        $this->migrator->add('integrations.smtp_last_message', null);

        $this->migrator->add('integrations.ai_enabled', false);
        $this->migrator->add('integrations.ai_api_key', null);
        $this->migrator->add('integrations.ai_endpoint', 'https://api.openai.com/v1/chat/completions');
        $this->migrator->add('integrations.ai_model', 'gpt-4o-mini');
        $this->migrator->add('integrations.ai_last_tested_at', null);
        $this->migrator->add('integrations.ai_last_status', null);
        $this->migrator->add('integrations.ai_last_message', null);

        $this->migrator->add('integrations.jira_enabled', false);
        $this->migrator->add('integrations.jira_base_url', null);
        $this->migrator->add('integrations.jira_email', null);
        $this->migrator->add('integrations.jira_api_token', null);
        $this->migrator->add('integrations.jira_last_tested_at', null);
        $this->migrator->add('integrations.jira_last_status', null);
        $this->migrator->add('integrations.jira_last_message', null);
    }
};
