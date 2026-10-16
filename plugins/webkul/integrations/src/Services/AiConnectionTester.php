<?php

namespace Webkul\Integrations\Services;

use Illuminate\Support\Facades\Http;
use Throwable;
use Webkul\Integrations\Settings\IntegrationSettings;
use Webkul\Integrations\Support\SecretVault;

class AiConnectionTester
{
    public function test(IntegrationSettings $settings): ConnectionTestResult
    {
        if (! $settings->ai_enabled) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.ai.disabled'));
        }

        $apiKey = SecretVault::decrypt($settings->ai_api_key);
        $endpoint = trim((string) ($settings->ai_endpoint ?: 'https://api.openai.com/v1/chat/completions'));
        $model = trim((string) ($settings->ai_model ?: 'gpt-4o-mini'));

        if ($apiKey === null || $apiKey === '' || $endpoint === '') {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.ai.incomplete'));
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->acceptJson()
                ->post($endpoint, [
                    'model'    => $model,
                    'messages' => [
                        [
                            'role'    => 'user',
                            'content' => 'Reply with exactly: OK',
                        ],
                    ],
                    'max_tokens' => 8,
                ]);

            if (! $response->successful()) {
                return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.ai.failed', [
                    'error' => $response->status().' '.$response->body(),
                ]));
            }

            $content = data_get($response->json(), 'choices.0.message.content');

            if (! is_string($content) || trim($content) === '') {
                return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.ai.empty'));
            }

            return ConnectionTestResult::ok(__('integrations::filament/pages/manage-integrations.testers.ai.success', [
                'model' => $model,
            ]));
        } catch (Throwable $exception) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.ai.failed', [
                'error' => $exception->getMessage(),
            ]));
        }
    }
}
