<?php

namespace Webkul\Support\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasIcon, HasLabel
{
    // Case order defines the sidebar group order (daily workflow first,
    // administration last). Values must stay stable: they are persisted
    // in translations and user navigation preferences.
    case Dashboard = 'dashboard';

    case Employee = 'employee';

    case Project = 'project';

    case TimeOff = 'time-off';

    case Recruitment = 'recruitment';

    case Contact = 'contact';

    case Sale = 'sale';

    case Purchase = 'purchase';

    case Inventory = 'inventory';

    case Manufacturing = 'manufacturing';

    case Maintenance = 'maintenance';

    case Invoice = 'invoice';

    case Accounting = 'accounting';

    case Pos = 'pos';

    case Website = 'website';

    case Barcode = 'barcode';

    case Plugin = 'plugin';

    case Setting = 'setting';

    case Help = 'help';

    public function getLabel(): string
    {
        return __('admin.navigation.'.$this->value);
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Dashboard     => 'heroicon-o-squares-2x2',
            self::Contact       => 'heroicon-o-users',
            self::Sale          => 'heroicon-o-shopping-bag',
            self::Purchase      => 'heroicon-o-shopping-cart',
            self::Maintenance   => 'heroicon-o-wrench-screwdriver',
            self::Manufacturing => 'heroicon-o-cog-6-tooth',
            self::Inventory     => 'heroicon-o-cube',
            self::Invoice       => 'heroicon-o-document-text',
            self::Accounting    => 'heroicon-o-calculator',
            self::Project       => 'heroicon-o-briefcase',
            self::Employee      => 'heroicon-o-identification',
            self::TimeOff       => 'heroicon-o-calendar-days',
            self::Recruitment   => 'heroicon-o-user-plus',
            self::Website       => 'heroicon-o-globe-alt',
            self::Barcode       => 'heroicon-o-qr-code',
            self::Pos           => 'heroicon-o-banknotes',
            self::Plugin        => 'heroicon-o-puzzle-piece',
            self::Setting       => 'heroicon-o-adjustments-horizontal',
            self::Help          => 'heroicon-o-question-mark-circle',
        };
    }
}
