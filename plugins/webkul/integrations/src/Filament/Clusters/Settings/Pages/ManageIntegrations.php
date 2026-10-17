<?php

namespace Webkul\Integrations\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Integrations\Services\AiConnectionTester;
use Webkul\Integrations\Services\ConnectionTestResult;
use Webkul\Integrations\Services\JiraConnectionTester;
use Webkul\Integrations\Services\SmtpConnectionTester;
use Webkul\Integrations\Settings\IntegrationSettings;
use Webkul\Integrations\Support\SecretVault;
use Webkul\Support\Filament\Clusters\Settings;

class ManageIntegrations extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 20;

    protected static string $settings = IntegrationSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_integrations_manage_integrations';
    }

    public static function getNavigationGroup(): string
    {
        return __('integrations::filament/pages/manage-integrations.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('integrations::filament/pages/manage-integrations.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('integrations::filament/pages/manage-integrations.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('integrations::filament/pages/manage-integrations.navigation.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('integrations::filament/pages/manage-integrations.sections.smtp.title'))
                    ->description(__('integrations::filament/pages/manage-integrations.sections.smtp.description'))
                    ->columns(2)
                    ->headerActions([
                        Action::make('test_smtp')
                            ->label(__('integrations::filament/pages/manage-integrations.actions.test'))
                            ->icon('heroicon-o-signal')
                            ->color('primary')
                            ->action(fn () => $this->runSmtpTest()),
                    ])
                    ->schema([
                        Toggle::make('smtp_enabled')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.enabled'))
                            ->columnSpanFull(),
                        TextInput::make('smtp_host')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_host'))
                            ->placeholder('smtp.office365.com'),
                        TextInput::make('smtp_port')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_port'))
                            ->numeric()
                            ->default(587),
                        Select::make('smtp_encryption')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_encryption'))
                            ->options([
                                'tls'  => 'TLS',
                                'ssl'  => 'SSL',
                                'null' => __('integrations::filament/pages/manage-integrations.fields.smtp_encryption_none'),
                            ])
                            ->native(),
                        TextInput::make('smtp_username')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_username'))
                            ->autocomplete('off'),
                        TextInput::make('smtp_password')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_password'))
                            ->password()
                            ->revealable()
                            ->helperText(__('integrations::filament/pages/manage-integrations.fields.secret_helper'))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->autocomplete('new-password'),
                        TextInput::make('smtp_from_address')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_from_address'))
                            ->email(),
                        TextInput::make('smtp_from_name')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.smtp_from_name')),
                        TextInput::make('smtp_last_status')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_status'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('smtp_last_tested_at')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_tested_at'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('smtp_last_message')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_message'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('integrations::filament/pages/manage-integrations.sections.ai.title'))
                    ->description(__('integrations::filament/pages/manage-integrations.sections.ai.description'))
                    ->columns(2)
                    ->headerActions([
                        Action::make('test_ai')
                            ->label(__('integrations::filament/pages/manage-integrations.actions.test'))
                            ->icon('heroicon-o-signal')
                            ->color('primary')
                            ->action(fn () => $this->runAiTest()),
                    ])
                    ->schema([
                        Toggle::make('ai_enabled')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.enabled'))
                            ->columnSpanFull(),
                        TextInput::make('ai_api_key')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.ai_api_key'))
                            ->password()
                            ->revealable()
                            ->helperText(__('integrations::filament/pages/manage-integrations.fields.secret_helper'))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->autocomplete('new-password')
                            ->columnSpanFull(),
                        TextInput::make('ai_endpoint')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.ai_endpoint'))
                            ->url()
                            ->columnSpanFull(),
                        TextInput::make('ai_model')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.ai_model'))
                            ->placeholder('gpt-4o-mini'),
                        TextInput::make('ai_last_status')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_status'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('ai_last_tested_at')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_tested_at'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('ai_last_message')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_message'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('integrations::filament/pages/manage-integrations.sections.jira.title'))
                    ->description(__('integrations::filament/pages/manage-integrations.sections.jira.description'))
                    ->columns(2)
                    ->headerActions([
                        Action::make('test_jira')
                            ->label(__('integrations::filament/pages/manage-integrations.actions.test'))
                            ->icon('heroicon-o-signal')
                            ->color('primary')
                            ->action(fn () => $this->runJiraTest()),
                    ])
                    ->schema([
                        Toggle::make('jira_enabled')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.enabled'))
                            ->columnSpanFull(),
                        TextInput::make('jira_base_url')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.jira_base_url'))
                            ->url()
                            ->placeholder('https://your-domain.atlassian.net')
                            ->columnSpanFull(),
                        TextInput::make('jira_email')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.jira_email'))
                            ->email(),
                        TextInput::make('jira_api_token')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.jira_api_token'))
                            ->password()
                            ->revealable()
                            ->helperText(__('integrations::filament/pages/manage-integrations.fields.secret_helper'))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->autocomplete('new-password'),
                        TextInput::make('jira_last_status')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_status'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('jira_last_tested_at')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_tested_at'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('jira_last_message')
                            ->label(__('integrations::filament/pages/manage-integrations.fields.last_message'))
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['smtp_password'] = null;
        $data['ai_api_key'] = null;
        $data['jira_api_token'] = null;

        if (($data['smtp_encryption'] ?? null) === null) {
            $data['smtp_encryption'] = 'null';
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var IntegrationSettings $settings */
        $settings = app(static::$settings);

        if (($data['smtp_encryption'] ?? null) === 'null') {
            $data['smtp_encryption'] = null;
        }

        if (array_key_exists('smtp_password', $data)) {
            $data['smtp_password'] = SecretVault::keepOrEncrypt($data['smtp_password'] ?? null, $settings->smtp_password);
        }

        if (array_key_exists('ai_api_key', $data)) {
            $data['ai_api_key'] = SecretVault::keepOrEncrypt($data['ai_api_key'] ?? null, $settings->ai_api_key);
        }

        if (array_key_exists('jira_api_token', $data)) {
            $data['jira_api_token'] = SecretVault::keepOrEncrypt($data['jira_api_token'] ?? null, $settings->jira_api_token);
        }

        return $data;
    }

    protected function runSmtpTest(): void
    {
        $this->save();

        $result = app(SmtpConnectionTester::class)->test(app(IntegrationSettings::class));

        $this->persistTestResult('smtp', $result);
    }

    protected function runAiTest(): void
    {
        $this->save();

        $result = app(AiConnectionTester::class)->test(app(IntegrationSettings::class));

        $this->persistTestResult('ai', $result);
    }

    protected function runJiraTest(): void
    {
        $this->save();

        $result = app(JiraConnectionTester::class)->test(app(IntegrationSettings::class));

        $this->persistTestResult('jira', $result);
    }

    protected function persistTestResult(string $connector, ConnectionTestResult $result): void
    {
        /** @var IntegrationSettings $settings */
        $settings = app(IntegrationSettings::class);
        $testedAt = now()->toDateTimeString();

        if ($connector === 'smtp') {
            $settings->smtp_last_tested_at = $testedAt;
            $settings->smtp_last_status = $result->status();
            $settings->smtp_last_message = $result->message;
            $settings->save();
        } elseif ($connector === 'ai') {
            $settings->ai_last_tested_at = $testedAt;
            $settings->ai_last_status = $result->status();
            $settings->ai_last_message = $result->message;
            $settings->save();
        } elseif ($connector === 'jira') {
            $settings->jira_last_tested_at = $testedAt;
            $settings->jira_last_status = $result->status();
            $settings->jira_last_message = $result->message;
            $settings->save();
        }

        $this->fillForm();

        $notification = Notification::make()
            ->title($result->success
                ? __('integrations::filament/pages/manage-integrations.notifications.success')
                : __('integrations::filament/pages/manage-integrations.notifications.failed'))
            ->body($result->message);

        if ($result->success) {
            $notification->success()->send();
        } else {
            $notification->danger()->send();
        }
    }
}
