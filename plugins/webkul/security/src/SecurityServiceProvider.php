<?php

namespace Webkul\Security;

use Filament\Panel;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Console\Commands\DatabaseBackupCleanCommand;
use Webkul\Security\Console\Commands\DatabaseBackupCommand;
use Webkul\Security\Console\Commands\SecurityHealthCheckCommand;
use Webkul\Security\Console\Commands\SyncRbacPermissionsCommand;
use Webkul\Security\Facades\Bouncer as BouncerFacade;
use Webkul\Security\Listeners\RecordSecurityAccessAudit;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Observers\RoleSecurityAuditObserver;
use Webkul\Security\Observers\UserSecurityAuditObserver;
use Webkul\Security\Policies\AuditTrailPolicy;

class SecurityServiceProvider extends PackageServiceProvider
{
    public static string $name = 'security';

    public static string $viewNamespace = 'security';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasRoute('api')
            ->runsMigrations()
            ->hasMigrations([
                '2024_11_11_112529_create_user_invitations_table',
                '2024_11_12_125715_create_teams_table',
                '2024_11_12_130019_create_user_team_table',
                '2024_12_10_101127_add_default_company_id_column_to_users_table',
                '2024_12_13_130906_add_partner_id_to_users_table',
                '2025_08_01_071239_alter_teams_table',
                '2025_08_01_073954_alter_users_table',
                '2025_08_21_082229_alter_roles_table',
                '2025_08_21_101646_alter_users_table',
                '2026_01_23_074142_add_multi_factor_auth_columns_in_users_table',
                '2026_10_16_160000_create_security_audit_trails_table',
                '2026_10_16_171000_add_project_id_to_teams_table',
                '2026_10_16_173000_create_security_blocked_ips_table',
            ])
            ->hasSettings([
                '2024_11_05_042358_create_user_settings',
                '2025_07_29_064223_create_currency_settings',
                '2026_10_20_120000_set_default_user_role_to_staff',
            ])
            ->runsSettings()
            ->hasConfigFile(['security-backup', 'security-demo', 'security-login'])
            ->hasCommands([
                SyncRbacPermissionsCommand::class,
                DatabaseBackupCommand::class,
                DatabaseBackupCleanCommand::class,
                SecurityHealthCheckCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        require_once __DIR__.'/Helpers/helpers.php';

        Gate::policy(AuditTrail::class, AuditTrailPolicy::class);

        User::observe(UserSecurityAuditObserver::class);
        Role::observe(RoleSecurityAuditObserver::class);

        Event::listen(Login::class, [RecordSecurityAccessAudit::class, 'handleLogin']);
        Event::listen(Failed::class, [RecordSecurityAccessAudit::class, 'handleFailed']);
        Event::listen(Logout::class, [RecordSecurityAccessAudit::class, 'handleLogout']);

        Gate::before(function ($user, string $ability) {
            if ($ability !== 'bypass_ownership_scope') {
                return null;
            }

            if ($user && method_exists($user, 'hasRole') && $user->hasRole(array_filter([
                config('filament-shield.super_admin.name'),
                'super_admin',
            ]))) {
                return true;
            }

            return null;
        });

        $this->app->booted(function (): void {
            if (! config('security-backup.enabled', true)) {
                return;
            }

            $scheduleAt = (string) config('security-backup.schedule_at', '02:00');

            /** @var Schedule $schedule */
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('security:database-backup')->dailyAt($scheduleAt)->withoutOverlapping();
            $schedule->command('security:database-backup-clean')->dailyAt('03:15')->withoutOverlapping();
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SecurityPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('bouncer', BouncerFacade::class);

        $this->app->singleton('bouncer', Bouncer::class);
        $this->app->singleton(PermissionRegistrar::class);
    }
}
