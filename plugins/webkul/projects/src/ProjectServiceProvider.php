<?php

namespace Webkul\Project;

use Filament\Infolists\Components\TextEntry;
use Filament\Panel;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Project\Console\Commands\NotifyExpiringProjectsCommand;
use Webkul\Project\Console\Commands\SyncTeamMembershipsCommand;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Services\OperationalScope;
use Webkul\Project\Services\TeamMembershipSynchronizer;
use Webkul\Security\Models\Team;

class ProjectServiceProvider extends PackageServiceProvider
{
    public static string $name = 'projects';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasRoute('api')
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                '2024_12_12_074920_create_projects_project_stages_table',
                '2024_12_12_100230_create_projects_tags_table',
                '2024_12_12_074929_create_projects_projects_table',
                '2024_12_12_074930_create_projects_milestones_table',
                '2024_12_12_100227_create_projects_user_project_favorites_table',
                '2024_12_12_100232_create_projects_project_tag_table',
                '2024_12_12_101340_create_projects_task_stages_table',
                '2024_12_12_101344_create_projects_tasks_table',
                '2024_12_12_101350_create_projects_task_users_table',
                '2024_12_12_101352_create_projects_task_tag_table',
                '2024_12_18_145142_add_columns_to_analytic_records_table',
                '2025_09_24_062711_remove_tags_column_from_projects_tasks_table',
                '2026_10_14_200000_add_project_value_to_projects_projects_table',
                '2026_10_16_140000_create_projects_project_employees_table',
                '2026_10_16_222630_add_operational_cost_to_projects_projects_table',
                '2026_10_20_130000_add_team_leader_to_projects_projects_table',
                '2026_10_27_120000_add_strategic_director_to_projects_projects_table',
                '2026_10_27_140000_create_projects_project_units_table',
            ])
            ->runsMigrations()
            ->hasCommands([
                NotifyExpiringProjectsCommand::class,
                SyncTeamMembershipsCommand::class,
            ])
            ->hasSettings([
                '2024_12_16_094021_create_project_task_settings',
                '2024_12_16_094021_create_project_time_settings',
            ])
            ->runsSettings()
            ->hasSeeder('Webkul\\Project\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Project::class, Task::class]);
                });
            })
            ->icon('projects');
    }

    public function packageBooted(): void
    {
        $this->app->booted(function (): void {
            if ($this->package->isInstalled()) {
                Schedule::command('projects:notify-expiring-projects')->dailyAt('08:00');
            }
        });

        if (class_exists(Team::class)) {
            Team::saved(function (Team $team): void {
                try {
                    app(TeamMembershipSynchronizer::class)->syncForTeam($team);
                } catch (\Throwable) {
                }
            });
        }

        if (class_exists(PartnerSchemaRegistry::class)) {
            PartnerSchemaRegistry::infolist('tabs.append', fn (): array => [
                Tab::make(__('projects::filament/resources/project.navigation.title'))
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Section::make(__('projects::filament/resources/project.customer-projects.title'))
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('customer_projects_count')
                                            ->label(__('projects::filament/resources/project.customer-projects.total-projects'))
                                            ->icon('heroicon-o-briefcase')
                                            ->state(fn (Partner $record): int => Schema::hasTable('projects_projects')
                                                ? Project::withoutGlobalScopes()->whereNull('deleted_at')->where('partner_id', $record->id)->count()
                                                : 0),

                                        TextEntry::make('customer_projects_total_value')
                                            ->label(__('projects::filament/resources/project.customer-projects.total-value'))
                                            ->icon('heroicon-o-banknotes')
                                            ->visible(fn (): bool => app(OperationalScope::class)->canViewProjectValue())
                                            ->state(function (Partner $record): string {
                                                if (! Schema::hasTable('projects_projects') || ! Schema::hasColumn('projects_projects', 'project_value')) {
                                                    return 'Rp 0';
                                                }

                                                $sum = (float) Project::withoutGlobalScopes()
                                                    ->whereNull('deleted_at')
                                                    ->where('partner_id', $record->id)
                                                    ->sum('project_value');

                                                return 'Rp '.number_format($sum, 0, ',', '.');
                                            }),

                                        TextEntry::make('customer_projects_list')
                                            ->label(__('projects::filament/resources/project.customer-projects.project-list'))
                                            ->state(function (Partner $record): string {
                                                if (! Schema::hasTable('projects_projects')) {
                                                    return '—';
                                                }

                                                $projects = Project::withoutGlobalScopes()
                                                    ->whereNull('deleted_at')
                                                    ->where('partner_id', $record->id)
                                                    ->orderBy('end_date')
                                                    ->get();

                                                if ($projects->isEmpty()) {
                                                    return '—';
                                                }

                                                $canViewProjectValue = app(OperationalScope::class)->canViewProjectValue();

                                                return $projects->map(function (Project $p) use ($canViewProjectValue): string {
                                                    $period = ($p->start_date && $p->end_date)
                                                        ? ' ('.$p->start_date->format('d M Y').' - '.$p->end_date->format('d M Y').')'
                                                        : '';
                                                    $val = $canViewProjectValue && $p->project_value ? ' — '.$p->formattedProjectValue() : '';

                                                    return $p->name.$period.$val;
                                                })->join(', ');
                                            }),
                                    ]),
                            ]),
                    ]),
            ], 30);
        }
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ProjectPlugin::make());
        });
    }
}
