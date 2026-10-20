<?php

namespace Webkul\Security\Services;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Support\AdminAccess;

class RolePermissionSynchronizer
{
    /**
     * Ensure Filament Shield permissions exist, sync Admin to all permissions,
     * and reset baseline operational roles to an exact permission set (sync, not grant-only).
     *
     * @return array{permissions_count: int, roles_synced: array<int, string>}
     */
    public function syncAll(bool $generateFromShield = false): array
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            return [
                'permissions_count' => 0,
                'roles_synced'      => [],
            ];
        }

        if ($generateFromShield) {
            try {
                Artisan::call('shield:generate', [
                    '--all'            => true,
                    '--option'         => 'permissions',
                    '--panel'          => 'admin',
                    '--no-interaction' => true,
                ]);
            } catch (Throwable) {
                // Continue with explicit + RoleResource permission seeding if shield:generate is unavailable.
            }
        }

        $guard = Utils::getFilamentAuthGuard() ?: 'web';

        foreach ($this->explicitPermissionNames() as $permName) {
            Permission::query()->firstOrCreate([
                'name'       => $permName,
                'guard_name' => $guard,
            ]);
        }

        try {
            foreach (RoleResource::getAllFormPermissions()->all() as $permName) {
                Permission::query()->firstOrCreate([
                    'name'       => $permName,
                    'guard_name' => $guard,
                ]);
            }
        } catch (Throwable) {
            // Panel context may be unavailable outside HTTP/Filament boot.
        }

        $allPermissions = Permission::query()->where('guard_name', $guard)->get();
        $rolesSynced = [];

        // 1. Admin — every permission
        $adminRole = Role::query()->firstOrCreate(
            ['name' => AdminAccess::adminRoleName(), 'guard_name' => $guard],
            ['is_default' => true]
        );
        $adminRole->syncPermissions($allPermissions);
        $rolesSynced[] = $adminRole->name;

        // 2. HRD — people / leave / performance (no customer project report, no OM widgets, no assets)
        $hrdRole = Role::query()->firstOrCreate(
            ['name' => 'HRD', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $hrdRole->syncPermissions($this->permissionsMatching($allPermissions, [
            'contains' => ['employee', 'attendance', 'performance', 'evaluation', 'time_off', 'recruitment'],
            'exact'    => [
                'page_project_weekly_operations_report',
                'page_support_my_day_dashboard',
                'page_evaluation_performance_evaluation_dashboard',
                'widget_project_attendance_presence_widget',
                'page_employee_people_dashboard',
                'widget_employee_people_stats_widget',
                'widget_employee_expiring_contracts_widget',
                'widget_employee_people_attention_widget',
            ],
        ]));
        $rolesSynced[] = $hrdRole->name;

        // 3. Operation Manager — project delivery + team HR approvals (not full Sales/Inventory ERP)
        $operationManagerRole = Role::query()->firstOrCreate(
            ['name' => 'Operation Manager', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $operationManagerRole->syncPermissions($this->permissionsMatching($allPermissions, [
            'contains' => [
                'project_project',
                'project_task',
                'project_timesheet',
                'timesheet',
                'partner_partner',
                'performance',
                'evaluation',
                'time_off',
                'attendance',
            ],
            'exact' => [
                'view_any_employee_employee',
                'view_employee_employee',
                // OM may manage employees, but EmployeeResource scopes
                // records to their projects (+ self/reports/created-by).
                // Delete is single-record soft-delete only: no bulk delete
                // and no force-delete (permanent removal stays Admin/HRD).
                'create_employee_employee',
                'update_employee_employee',
                'delete_employee_employee',
                'view_any_employee_department',
                'view_employee_department',
                'page_project_dashboard',
                'page_project_assign_project_employees',
                'page_project_weekly_operations_report',
                'page_project_customer_project_weekly_report',
                'page_evaluation_performance_evaluation_dashboard',
                'page_support_my_day_dashboard',
                'page_support_team_board',
                'page_attendance_clock_page',
                'widget_project_operation_manager_workload_widget',
                'widget_project_operation_manager_actions_widget',
                'widget_project_attendance_presence_widget',
                'widget_project_expiring_projects_widget',
                'page_employee_people_dashboard',
                'widget_employee_people_stats_widget',
                'widget_employee_expiring_contracts_widget',
                'widget_employee_people_attention_widget',
            ],
        ]));
        $rolesSynced[] = $operationManagerRole->name;

        // 4. Accounting
        $accountingRole = Role::query()->firstOrCreate(
            ['name' => 'Accounting', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $accountingRole->syncPermissions($this->permissionsMatching($allPermissions, [
            'contains' => ['invoice', 'account', 'payment', 'bill', 'partner', 'contact', 'product'],
            'exact'    => [
                'page_support_my_day_dashboard',
                'page_support_team_board',
                'page_project_dashboard',
                'view_any_project_project',
                'view_project_project',
                'page_attendance_clock_page',
            ],
        ]));
        $rolesSynced[] = $accountingRole->name;

        // 5. Kasir
        $kasirRole = Role::query()->firstOrCreate(
            ['name' => 'Kasir', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $kasirRole->syncPermissions($this->permissionsMatching($allPermissions, [
            'contains' => ['_pos_'],
            'exact'    => [
                'page_pos_pos_terminal_page',
                'page_attendance_clock_page',
                'page_support_my_day_dashboard',
                'view_any_product_product',
                'view_product_product',
                'view_any_partner_partner',
                'view_partner_partner',
                'create_partner_partner',
            ],
        ]));
        $rolesSynced[] = $kasirRole->name;

        // 6. Staff — self-service (+ task/timesheet updates); no eval dashboard / company-wide HR
        $staffRole = Role::query()->firstOrCreate(
            ['name' => 'Staff', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $staffRole->syncPermissions($this->permissionsMatching($allPermissions, [
            'exact' => [
                'page_attendance_clock_page',
                'page_support_my_day_dashboard',
                'page_support_team_board',
                'view_any_attendance_attendance',
                'create_attendance_attendance',
                'view_any_time_off_my::time::off',
                'view_time_off_my::time::off',
                'create_time_off_my::time::off',
                'update_time_off_my::time::off',
                'delete_time_off_my::time::off',
                'view_any_time_off_my::allocation',
                'view_time_off_my::allocation',
                'view_performance_performance::plan',
                'view_evaluation_evaluation',
                'view_any_project_project',
                'view_project_project',
                'view_any_project_task',
                'view_project_task',
                'update_project_task',
                'view_any_project_timesheet',
                'create_project_timesheet',
                'update_project_timesheet',
            ],
        ]));
        $rolesSynced[] = $staffRole->name;

        // Hard revoke admin-only security features from every non-Admin role.
        $adminOnlyPermissions = $allPermissions->whereIn('name', AdminAccess::adminOnlyPermissionNames());

        foreach ([$hrdRole, $operationManagerRole, $accountingRole, $kasirRole, $staffRole] as $nonAdminRole) {
            if ($adminOnlyPermissions->isNotEmpty()) {
                $nonAdminRole->revokePermissionTo($adminOnlyPermissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return [
            'permissions_count' => $allPermissions->count(),
            'roles_synced'      => $rolesSynced,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function explicitPermissionNames(): array
    {
        return [
            'view_any_security_user', 'view_security_user', 'create_security_user', 'update_security_user', 'delete_security_user', 'delete_any_security_user', 'restore_security_user', 'restore_any_security_user', 'force_delete_security_user', 'force_delete_any_security_user',
            'view_any_security_team', 'view_security_team', 'create_security_team', 'update_security_team', 'delete_security_team', 'delete_any_security_team',
            'view_any_security_company', 'view_security_company', 'create_security_company', 'update_security_company', 'delete_security_company', 'delete_any_security_company',
            'view_any_security_audit::trail', 'view_security_audit::trail',
            'page_security_manage_database_backups',
            'page_security_manage_blocked_ips',
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role', 'force_delete_role', 'force_delete_any_role', 'restore_role', 'restore_any_role', 'reorder_role',
            'view_any_employee_employee', 'view_employee_employee', 'create_employee_employee', 'update_employee_employee', 'delete_employee_employee', 'delete_any_employee_employee',
            'view_any_employee_department', 'view_employee_department', 'create_employee_department', 'update_employee_department', 'delete_employee_department', 'delete_any_employee_department',
            'view_any_attendance_attendance', 'create_attendance_attendance', 'update_attendance_attendance', 'delete_attendance_attendance', 'delete_any_attendance_attendance',
            'view_any_attendance_attendance::site', 'create_attendance_attendance::site', 'update_attendance_attendance::site', 'delete_attendance_attendance::site', 'delete_any_attendance_attendance::site',
            'page_attendance_clock_page',
            'view_any_time_off_time::off', 'view_time_off_time::off', 'create_time_off_time::off', 'update_time_off_time::off', 'delete_time_off_time::off', 'delete_any_time_off_time::off',
            'view_any_time_off_my::time::off', 'view_time_off_my::time::off', 'create_time_off_my::time::off', 'update_time_off_my::time::off', 'delete_time_off_my::time::off', 'delete_any_time_off_my::time::off',
            'view_any_time_off_by::employee', 'view_time_off_by::employee', 'create_time_off_by::employee', 'update_time_off_by::employee', 'delete_time_off_by::employee', 'delete_any_time_off_by::employee',
            'view_any_time_off_allocation', 'view_time_off_allocation', 'create_time_off_allocation', 'update_time_off_allocation', 'delete_time_off_allocation', 'delete_any_time_off_allocation',
            'view_any_time_off_my::allocation', 'view_time_off_my::allocation', 'create_time_off_my::allocation', 'update_time_off_my::allocation', 'delete_time_off_my::allocation', 'delete_any_time_off_my::allocation',
            'view_any_performance_performance::plan', 'view_performance_performance::plan', 'create_performance_performance::plan', 'update_performance_performance::plan', 'delete_performance_performance::plan', 'delete_any_performance_performance::plan',
            'view_any_evaluation_evaluation', 'view_evaluation_evaluation', 'create_evaluation_evaluation', 'update_evaluation_evaluation', 'delete_evaluation_evaluation', 'delete_any_evaluation_evaluation',
            'page_evaluation_performance_evaluation_dashboard',
            'page_project_dashboard',
            'page_project_assign_project_employees',
            'widget_project_operation_manager_workload_widget',
            'widget_project_operation_manager_actions_widget',
            'widget_project_attendance_presence_widget',
            'widget_project_expiring_projects_widget',
            'page_employee_people_dashboard',
            'widget_employee_people_stats_widget',
            'widget_employee_expiring_contracts_widget',
            'widget_employee_people_attention_widget',
            'page_integrations_manage_integrations',
            'page_support_welcome_dashboard',
            'page_support_my_day_dashboard',
            'page_support_team_board',
            'page_project_weekly_operations_report',
            'page_project_customer_project_weekly_report',
            'view_any_project_project', 'view_project_project', 'create_project_project', 'update_project_project', 'delete_project_project', 'delete_any_project_project',
            'view_any_project_task', 'view_project_task', 'create_project_task', 'update_project_task', 'delete_project_task', 'delete_any_project_task',
            'view_any_project_timesheet', 'create_project_timesheet', 'update_project_timesheet', 'delete_project_timesheet', 'delete_any_project_timesheet',
            'view_any_partner_partner', 'view_partner_partner', 'create_partner_partner', 'update_partner_partner', 'delete_partner_partner', 'delete_any_partner_partner',
            'view_any_product_product', 'view_product_product', 'create_product_product', 'update_product_product', 'delete_product_product', 'delete_any_product_product',
            'view_any_maintenance_equipment', 'view_maintenance_equipment', 'create_maintenance_equipment', 'update_maintenance_equipment', 'delete_maintenance_equipment', 'delete_any_maintenance_equipment',
            'page_pos_pos_terminal_page',
            'view_any_pos_pos::register', 'view_pos_pos::register', 'create_pos_pos::register', 'update_pos_pos::register', 'delete_pos_pos::register', 'delete_any_pos_pos::register',
            'view_any_pos_pos::payment::method', 'view_pos_pos::payment::method', 'create_pos_pos::payment::method', 'update_pos_pos::payment::method', 'delete_pos_pos::payment::method', 'delete_any_pos_pos::payment::method',
            'view_any_pos_pos::session', 'view_pos_pos::session', 'create_pos_pos::session', 'update_pos_pos::session', 'delete_pos_pos::session', 'delete_any_pos_pos::session',
            'view_any_pos_pos::order', 'view_pos_pos::order', 'create_pos_pos::order', 'update_pos_pos::order', 'delete_pos_pos::order', 'delete_any_pos_pos::order',
        ];
    }

    /**
     * @param  Collection<int, Permission>  $allPermissions
     * @param  array{contains?: array<int, string>, exact?: array<int, string>}  $rules
     * @return Collection<int, Permission>
     */
    protected function permissionsMatching(Collection $allPermissions, array $rules): Collection
    {
        $contains = $rules['contains'] ?? [];
        $exact = $rules['exact'] ?? [];

        return $allPermissions
            ->filter(function (Permission $permission) use ($contains, $exact): bool {
                if (in_array($permission->name, $exact, true)) {
                    return true;
                }

                foreach ($contains as $needle) {
                    if (str_contains($permission->name, $needle)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }
}
