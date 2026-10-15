<?php

namespace Webkul\Support\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Support\Settings\BrandSettings;

class ManageBranding extends SettingsPage
{
    use HasPageShield;

    protected static ?string $cluster = Settings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 10;

    protected static string $settings = BrandSettings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_support_manage_branding';
    }

    public static function getNavigationGroup(): string
    {
        return __('support::filament/clusters/manage-branding.group');
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('support::filament/clusters/manage-branding.breadcrumb'),
        ];
    }

    public function getTitle(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/clusters/manage-branding.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/clusters/manage-branding.form.sections.logo.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.logo.description'))
                    ->columns(2)
                    ->schema([
                        FileUpload::make('light_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.light-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.light-logo-helper'))
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/bmp',
                                'image/webp',
                            ])
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('dark_logo')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.dark-logo'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.dark-logo-helper'))
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/bmp',
                                'image/webp',
                            ])
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        FileUpload::make('favicon')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.favicon'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.favicon-helper'))
                            ->image()
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                                'image/bmp',
                                'image/webp',
                            ])
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public'),
                        TextInput::make('logo_height')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.logo-height'))
                            ->helperText(__('support::filament/clusters/manage-branding.form.fields.logo-height-helper'))
                            ->placeholder('2rem'),
                    ]),
                Section::make(__('support::filament/clusters/manage-branding.form.sections.colors.title'))
                    ->description(__('support::filament/clusters/manage-branding.form.sections.colors.description'))
                    ->columns(3)
                    ->headerActions([
                        Action::make('preset_indigo')
                            ->label('Aura Indigo')
                            ->icon('heroicon-o-sparkles')
                            ->color('primary')
                            ->button()
                            ->size('sm')
                            ->action(fn (Set $set) => $this->applyPreset($set, 'indigo')),
                        Action::make('preset_emerald')
                            ->label('Emerald FinTech')
                            ->icon('heroicon-o-banknotes')
                            ->color('success')
                            ->button()
                            ->size('sm')
                            ->action(fn (Set $set) => $this->applyPreset($set, 'emerald')),
                        Action::make('preset_ocean')
                            ->label('Ocean SaaS')
                            ->icon('heroicon-o-globe-alt')
                            ->color('info')
                            ->button()
                            ->size('sm')
                            ->action(fn (Set $set) => $this->applyPreset($set, 'ocean')),
                        Action::make('preset_violet')
                            ->label('Royal Violet')
                            ->icon('heroicon-o-swatch')
                            ->color('warning')
                            ->button()
                            ->size('sm')
                            ->action(fn (Set $set) => $this->applyPreset($set, 'violet')),
                        Action::make('preset_slate')
                            ->label('Minimal Monochrome')
                            ->icon('heroicon-o-cube-transparent')
                            ->color('gray')
                            ->button()
                            ->size('sm')
                            ->action(fn (Set $set) => $this->applyPreset($set, 'slate')),
                        Action::make('reset')
                            ->label(__('support::filament/clusters/manage-branding.actions.reset.label'))
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->link()
                            ->requiresConfirmation()
                            ->action(function (Set $set): void {
                                foreach ($this->getDefaultColors() as $field => $hex) {
                                    $set($field, $hex);
                                }
                            }),
                    ])
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.primary-color'))
                            ->hexColor(),
                        ColorPicker::make('gray_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.gray-color'))
                            ->hexColor(),
                        ColorPicker::make('danger_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.danger-color'))
                            ->hexColor(),
                        ColorPicker::make('info_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.info-color'))
                            ->hexColor(),
                        ColorPicker::make('success_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.success-color'))
                            ->hexColor(),
                        ColorPicker::make('warning_color')
                            ->label(__('support::filament/clusters/manage-branding.form.fields.warning-color'))
                            ->hexColor(),
                    ]),
            ]);
    }

    private function applyPreset(Set $set, string $preset): void
    {
        $presets = [
            'indigo' => [
                'primary_color' => Color::convertToHex(Color::Indigo[600]),
                'gray_color'    => Color::convertToHex(Color::Slate[600]),
                'danger_color'  => Color::convertToHex(Color::Rose[600]),
                'info_color'    => Color::convertToHex(Color::Sky[600]),
                'success_color' => Color::convertToHex(Color::Emerald[600]),
                'warning_color' => Color::convertToHex(Color::Amber[600]),
            ],
            'emerald' => [
                'primary_color' => Color::convertToHex(Color::Emerald[600]),
                'gray_color'    => Color::convertToHex(Color::Slate[600]),
                'danger_color'  => Color::convertToHex(Color::Rose[600]),
                'info_color'    => Color::convertToHex(Color::Teal[600]),
                'success_color' => Color::convertToHex(Color::Green[600]),
                'warning_color' => Color::convertToHex(Color::Amber[600]),
            ],
            'ocean' => [
                'primary_color' => Color::convertToHex(Color::Sky[600]),
                'gray_color'    => Color::convertToHex(Color::Slate[600]),
                'danger_color'  => Color::convertToHex(Color::Red[600]),
                'info_color'    => Color::convertToHex(Color::Cyan[600]),
                'success_color' => Color::convertToHex(Color::Emerald[600]),
                'warning_color' => Color::convertToHex(Color::Amber[600]),
            ],
            'violet' => [
                'primary_color' => Color::convertToHex(Color::Violet[600]),
                'gray_color'    => Color::convertToHex(Color::Zinc[600]),
                'danger_color'  => Color::convertToHex(Color::Rose[600]),
                'info_color'    => Color::convertToHex(Color::Indigo[600]),
                'success_color' => Color::convertToHex(Color::Emerald[600]),
                'warning_color' => Color::convertToHex(Color::Amber[600]),
            ],
            'slate' => [
                'primary_color' => Color::convertToHex(Color::Slate[800]),
                'gray_color'    => Color::convertToHex(Color::Zinc[600]),
                'danger_color'  => Color::convertToHex(Color::Red[600]),
                'info_color'    => Color::convertToHex(Color::Blue[600]),
                'success_color' => Color::convertToHex(Color::Emerald[600]),
                'warning_color' => Color::convertToHex(Color::Amber[600]),
            ],
        ];

        foreach ($presets[$preset] ?? $this->getDefaultColors() as $field => $hex) {
            $set($field, $hex);
        }
    }

    private function getDefaultColors(): array
    {
        return [
            'primary_color' => Color::convertToHex(Color::Indigo[600]),
            'gray_color'    => Color::convertToHex(Color::Slate[600]),
            'danger_color'  => Color::convertToHex(Color::Rose[600]),
            'info_color'    => Color::convertToHex(Color::Sky[600]),
            'success_color' => Color::convertToHex(Color::Emerald[600]),
            'warning_color' => Color::convertToHex(Color::Amber[600]),
        ];
    }
}
