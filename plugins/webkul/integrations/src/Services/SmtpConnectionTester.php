<?php

namespace Webkul\Integrations\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Webkul\Integrations\Settings\IntegrationSettings;
use Webkul\Integrations\Support\SecretVault;

class SmtpConnectionTester
{
    public function test(IntegrationSettings $settings, ?string $recipient = null): ConnectionTestResult
    {
        if (! $settings->smtp_enabled) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.smtp.disabled'));
        }

        $host = trim((string) $settings->smtp_host);
        $username = trim((string) $settings->smtp_username);
        $fromAddress = trim((string) $settings->smtp_from_address);
        $password = SecretVault::decrypt($settings->smtp_password);

        if ($host === '' || $fromAddress === '' || $password === null || $password === '') {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.smtp.incomplete'));
        }

        $to = $recipient ?: Auth::user()?->email;

        if (! filled($to)) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.smtp.no-recipient'));
        }

        config([
            'mail.mailers.integrations_smtp' => [
                'transport'  => 'smtp',
                'host'       => $host,
                'port'       => (int) $settings->smtp_port,
                'encryption' => filled($settings->smtp_encryption) ? $settings->smtp_encryption : null,
                'username'   => $username !== '' ? $username : null,
                'password'   => $password,
                'timeout'    => 15,
            ],
            'mail.from.address' => $fromAddress,
            'mail.from.name'    => $settings->smtp_from_name ?: config('app.name'),
        ]);

        try {
            Mail::mailer('integrations_smtp')->raw(
                __('integrations::filament/pages/manage-integrations.testers.smtp.body'),
                function ($message) use ($to, $fromAddress, $settings): void {
                    $message
                        ->to($to)
                        ->from($fromAddress, $settings->smtp_from_name ?: config('app.name'))
                        ->subject(__('integrations::filament/pages/manage-integrations.testers.smtp.subject'));
                }
            );

            return ConnectionTestResult::ok(__('integrations::filament/pages/manage-integrations.testers.smtp.success', [
                'email' => $to,
            ]));
        } catch (Throwable $exception) {
            return ConnectionTestResult::fail(__('integrations::filament/pages/manage-integrations.testers.smtp.failed', [
                'error' => $exception->getMessage(),
            ]));
        }
    }
}
