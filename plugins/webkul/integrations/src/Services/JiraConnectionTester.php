<?php

namespace Webkul\Integrations\Services;

use Illuminate\Support\Facades\Http;
use Throwable;
use Webkul\Integrations\Settings\IntegrationSettings;
use Webkul\Integrations\Support\SecretVault;

class JiraConnectionTester
{
    public function test(IntegrationSettings $settings): ConnectionTestResult
    {
        if (! $settings->jira_enabled) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.jira.disabled'));
        }

        $baseUrl = rtrim(trim((string) $settings->jira_base_url), '/');
        $email = trim((string) $settings->jira_email);
        $token = SecretVault::decrypt($settings->jira_api_token);

        if ($baseUrl === '' || $email === '' || $token === null || $token === '') {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.jira.incomplete'));
        }

        try {
            $response = Http::withBasicAuth($email, $token)
                ->timeout(15)
                ->acceptJson()
                ->get("{$baseUrl}/rest/api/3/myself");

            if (! $response->successful()) {
                return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.jira.failed', [
                    'error' => $response->status().' '.$response->body(),
                ]));
            }

            $displayName = data_get($response->json(), 'displayName')
                ?? data_get($response->json(), 'emailAddress')
                ?? $email;

            return ConnectionTestResult::ok(__('integrations::filament/pages/manage-integrations.testers.jira.success', [
                'account' => $displayName,
            ]));
        } catch (Throwable $exception) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.jira.failed', [
                'error' => $exception->getMessage(),
            ]));
        }
    }
}
