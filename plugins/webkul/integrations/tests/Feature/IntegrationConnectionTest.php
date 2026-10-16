<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Webkul\Integrations\Services\AiConnectionTester;
use Webkul\Integrations\Services\JiraConnectionTester;
use Webkul\Integrations\Services\SmtpConnectionTester;
use Webkul\Integrations\Settings\IntegrationSettings;
use Webkul\Integrations\Support\SecretVault;

function fakeIntegrationSettings(array $overrides = []): IntegrationSettings
{
    return IntegrationSettings::fake(array_merge([
        'smtp_enabled'        => false,
        'smtp_host'           => null,
        'smtp_port'           => 587,
        'smtp_encryption'     => null,
        'smtp_username'       => null,
        'smtp_password'       => null,
        'smtp_from_address'   => null,
        'smtp_from_name'      => null,
        'smtp_last_tested_at' => null,
        'smtp_last_status'    => null,
        'smtp_last_message'   => null,
        'ai_enabled'          => false,
        'ai_api_key'          => null,
        'ai_endpoint'         => 'https://api.openai.com/v1/chat/completions',
        'ai_model'            => 'gpt-4o-mini',
        'ai_last_tested_at'   => null,
        'ai_last_status'      => null,
        'ai_last_message'     => null,
        'jira_enabled'        => false,
        'jira_base_url'       => null,
        'jira_email'          => null,
        'jira_api_token'      => null,
        'jira_last_tested_at' => null,
        'jira_last_status'    => null,
        'jira_last_message'   => null,
    ], $overrides), false);
}

it('rejects smtp test when the connector is disabled', function () {
    $settings = fakeIntegrationSettings([
        'smtp_host'         => 'smtp.example.com',
        'smtp_password'     => SecretVault::encrypt('secret'),
        'smtp_from_address' => 'noreply@example.com',
    ]);

    $result = app(SmtpConnectionTester::class)->test($settings, 'admin@example.com');

    expect($result->success)->toBeFalse();
});

it('sends a smtp test mail through the integrations mailer', function () {
    Mail::fake();

    $settings = fakeIntegrationSettings([
        'smtp_enabled'      => true,
        'smtp_host'         => 'smtp.example.com',
        'smtp_port'         => 587,
        'smtp_encryption'   => 'tls',
        'smtp_username'     => 'noreply@example.com',
        'smtp_password'     => SecretVault::encrypt('secret'),
        'smtp_from_address' => 'noreply@example.com',
        'smtp_from_name'    => 'auraERP',
    ]);

    $result = app(SmtpConnectionTester::class)->test($settings, 'admin@example.com');

    expect($result->success)->toBeTrue()
        ->and($result->message)->toContain('admin@example.com');
});

it('reports successful auraAI connection from a fake provider response', function () {
    Http::fake([
        'https://api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'OK']],
            ],
        ], 200),
    ]);

    $settings = fakeIntegrationSettings([
        'ai_enabled'  => true,
        'ai_api_key'  => SecretVault::encrypt('test-key'),
        'ai_endpoint' => 'https://api.openai.com/v1/chat/completions',
        'ai_model'    => 'gpt-4o-mini',
    ]);

    $result = app(AiConnectionTester::class)->test($settings);

    expect($result->success)->toBeTrue()
        ->and($result->message)->toContain('gpt-4o-mini');
});

it('authenticates to jira myself endpoint with basic auth', function () {
    Http::fake([
        'https://acme.atlassian.net/rest/api/3/myself' => Http::response([
            'displayName'  => 'Ops Admin',
            'emailAddress' => 'ops@example.com',
        ], 200),
    ]);

    $settings = fakeIntegrationSettings([
        'jira_enabled'   => true,
        'jira_base_url'  => 'https://acme.atlassian.net',
        'jira_email'     => 'ops@example.com',
        'jira_api_token' => SecretVault::encrypt('jira-token'),
    ]);

    $result = app(JiraConnectionTester::class)->test($settings);

    expect($result->success)->toBeTrue()
        ->and($result->message)->toContain('Ops Admin');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://acme.atlassian.net/rest/api/3/myself'
            && $request->hasHeader('Authorization');
    });
});

it('keeps an existing encrypted secret when the form field is blank', function () {
    $existing = SecretVault::encrypt('kept-secret');

    expect(SecretVault::keepOrEncrypt(null, $existing))->toBe($existing)
        ->and(SecretVault::keepOrEncrypt('', $existing))->toBe($existing)
        ->and(SecretVault::decrypt(SecretVault::keepOrEncrypt('new-secret', $existing)))->toBe('new-secret');
});
