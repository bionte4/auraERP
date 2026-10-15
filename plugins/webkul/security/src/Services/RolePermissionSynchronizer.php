<?php

namespace Webkul\Security\Services;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;
use Webkul\Security\Filament\Resources\RoleResource;

class RolePermissionSynchronizer
{
    /**
     * Ensure all Filament Shield permissions exist, sync all permissions to Admin,
     * and ensure standard operational roles (HRD, Kasir, Staff) exist with baseline permissions.
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
                // Continue with explicit + RoleResource permission seeding if shield:generate is unavailable in context.
            }
        }

        $guard = Utils::getFilamentAuthGuard() ?: 'web';

        $explicitPermissions = [
            // Security
            'view_any_security_user', 'view_security_user', 'create_security_user', 'update_security_user', 'delete_security_user', 'delete_any_security_user', 'restore_security_user', 'restore_any_security_user', 'force_delete_security_user', 'force_delete_any_security_user',
            'view_any_security_team', 'view_security_team', 'create_security_team', 'update_security_team', 'delete_security_team', 'delete_any_security_team',
            'view_any_security_company', 'view_security_company', 'create_security_company', 'update_security_company', 'delete_security_company', 'delete_any_security_company',
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role', 'force_delete_role', 'force_delete_any_role', 'restore_role', 'restore_any_role', 'reorder_role',

            // Employees & HR
            'view_any_employee_employee', 'view_employee_employee', 'create_employee_employee', 'update_employee_employee', 'delete_employee_employee', 'delete_any_employee_employee',
            'view_any_employee_department', 'view_employee_department', 'create_employee_department', 'update_employee_department', 'delete_employee_department', 'delete_any_employee_department',

            // Attendance
            'view_any_attendance_attendance', 'create_attendance_attendance', 'update_attendance_attendance', 'delete_attendance_attendance', 'delete_any_attendance_attendance',
            'view_any_attendance_attendance::site', 'create_attendance_attendance::site', 'update_attendance_attendance::site', 'delete_attendance_attendance::site', 'delete_any_attendance_attendance::site',
            'page_attendance_clock_page',

            // Time Off (Cuti / Izin)
            'view_any_time_off_time::off', 'view_time_off_time::off', 'create_time_off_time::off', 'update_time_off_time::off', 'delete_time_off_time::off', 'delete_any_time_off_time::off',
            'view_any_time_off_my::time::off', 'view_time_off_my::time::off', 'create_time_off_my::time::off', 'update_time_off_my::time::off', 'delete_time_off_my::time::off', 'delete_any_time_off_my::time::off',
            'view_any_time_off_by::employee', 'view_time_off_by::employee', 'create_time_off_by::employee', 'update_time_off_by::employee', 'delete_time_off_by::employee', 'delete_any_time_off_by::employee',
            'view_any_time_off_allocation', 'view_time_off_allocation', 'create_time_off_allocation', 'update_time_off_allocation', 'delete_time_off_allocation', 'delete_any_time_off_allocation',
            'view_any_time_off_my::allocation', 'view_time_off_my::allocation', 'create_time_off_my::allocation', 'update_time_off_my::allocation', 'delete_time_off_my::allocation', 'delete_any_time_off_my::allocation',

            // Performance & Evaluation
            'view_any_performance_performance::plan', 'view_performance_performance::plan', 'create_performance_performance::plan', 'update_performance_performance::plan', 'delete_performance_performance::plan', 'delete_any_performance_performance::plan',
            'view_any_evaluation_evaluation', 'view_evaluation_evaluation', 'create_evaluation_evaluation', 'update_evaluation_evaluation', 'delete_evaluation_evaluation', 'delete_any_evaluation_evaluation',
            'page_evaluation_performance_evaluation_dashboard',

            // Projects & Timesheets
            'view_any_project_project', 'view_project_project', 'create_project_project', 'update_project_project', 'delete_project_project', 'delete_any_project_project',
            'view_any_project_task', 'view_project_task', 'create_project_task', 'update_project_task', 'delete_project_task', 'delete_any_project_task',
            'view_any_project_timesheet', 'create_project_timesheet', 'update_project_timesheet', 'delete_project_timesheet', 'delete_any_project_timesheet',

            // Partners / Contacts
            'view_any_partner_partner', 'view_partner_partner', 'create_partner_partner', 'update_partner_partner', 'delete_partner_partner', 'delete_any_partner_partner',

            // Products & Inventory
            'view_any_product_product', 'view_product_product', 'create_product_product', 'update_product_product', 'delete_product_product', 'delete_any_product_product',

            // Maintenance / Assets
            'view_any_maintenance_equipment', 'view_maintenance_equipment', 'create_maintenance_equipment', 'update_maintenance_equipment', 'delete_maintenance_equipment', 'delete_any_maintenance_equipment',

            // POS (Point of Sale)
            'page_pos_pos_terminal_page',
            'view_any_pos_pos::register', 'view_pos_pos::register', 'create_pos_pos::register', 'update_pos_pos::register', 'delete_pos_pos::register', 'delete_any_pos_pos::register',
            'view_any_pos_pos::payment::method', 'view_pos_pos::payment::method', 'create_pos_pos::payment::method', 'update_pos_pos::payment::method', 'delete_pos_pos::payment::method', 'delete_any_pos_pos::payment::method',
            'view_any_pos_pos::session', 'view_pos_pos::session', 'create_pos_pos::session', 'update_pos_pos::session', 'delete_pos_pos::session', 'delete_any_pos_pos::session',
            'view_any_pos_pos::order', 'view_pos_pos::order', 'create_pos_pos::order', 'update_pos_pos::order', 'delete_pos_pos::order', 'delete_any_pos_pos::order',
        ];

        try {
            $discoveredPermissions = RoleResource::getAllFormPermissions()->all();
            $explicitPermissions = array_values(array_unique(array_merge($explicitPermissions, $discoveredPermissions)));
        } catch (Throwable) {
            // Safely fallback to explicit permissions if Filament panel context is not booted.
        }

        foreach ($explicitPermissions as $permName) {
            Permission::query()->firstOrCreate([
                'name'       => $permName,
                'guard_name' => $guard,
            ]);
        }

        $allPermissions = Permission::query()->where('guard_name', $guard)->get();
        $rolesSynced = [];

        // 1. Admin Role — receives all permissions
        $adminRoleName = config('filament-shield.panel_user.name', 'Admin') ?: 'Admin';
        $adminRole = Role::query()->firstOrCreate(
            ['name' => $adminRoleName, 'guard_name' => $guard],
            ['is_default' => true]
        );
        $adminRole->syncPermissions($allPermissions);
        $rolesSynced[] = $adminRole->name;

        // 2. HRD Role — default permissions if role doesn't have custom ones or is newly created
        $hrdRole = Role::query()->firstOrCreate(
            ['name' => 'HRD', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $hrdPermissions = $allPermissions->filter(fn (Permission $p): bool => str_contains($p->name, 'employee')
            || str_contains($p->name, 'attendance')
            || str_contains($p->name, 'performance')
            || str_contains($p->name, 'evaluation')
            || str_contains($p->name, 'time_off')
            || str_contains($p->name, 'recruitment')
            || str_contains($p->name, 'maintenance_equipment')
        );
        $hrdRole->givePermissionTo($hrdPermissions);
        $rolesSynced[] = $hrdRole->name;

        // 3. Operation Manager Role — manages Projects, Tasks, Timesheets, Inventory/Warehouse, Maintenance, Purchases, Sales, and Team HR/Evaluations/Approvals
        $operationManagerRole = Role::query()->firstOrCreate(
            ['name' => 'Operation Manager', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $operationManagerPermissions = $allPermissions->filter(fn (Permission $p): bool => str_contains($p->name, 'project')
            || str_contains($p->name, 'timesheet')
            || str_contains($p->name, 'inventory')
            || str_contains($p->name, 'product')
            || str_contains($p->name, 'maintenance')
            || str_contains($p->name, 'purchase')
            || str_contains($p->name, 'sale')
            || str_contains($p->name, 'partner')
            || str_contains($p->name, 'contact')
            || str_contains($p->name, 'performance')
            || str_contains($p->name, 'evaluation')
            || str_contains($p->name, 'time_off')
            || str_contains($p->name, 'attendance')
            || in_array($p->name, [
                'view_any_employee_employee',
                'view_employee_employee',
                'view_any_employee_department',
                'view_employee_department',
                'page_project_dashboard',
                'page_evaluation_performance_evaluation_dashboard',
                'page_attendance_clock_page',
            ], true)
        );
        $operationManagerRole->givePermissionTo($operationManagerPermissions);
        $rolesSynced[] = $operationManagerRole->name;

        // 4. Kasir (POS) Role
        $kasirRole = Role::query()->firstOrCreate(
            ['name' => 'Kasir', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $kasirPermissions = $allPermissions->filter(fn (Permission $p): bool => str_contains($p->name, '_pos_')
            || in_array($p->name, [
                'page_pos_pos_terminal_page',
                'page_attendance_clock_page',
                'view_any_product_product',
                'view_product_product',
                'view_any_partner_partner',
                'view_partner_partner',
                'create_partner_partner',
            ], true)
        );
        $kasirRole->givePermissionTo($kasirPermissions);
        $rolesSynced[] = $kasirRole->name;

        // 4. Staff / Karyawan Role
        $staffRole = Role::query()->firstOrCreate(
            ['name' => 'Staff', 'guard_name' => $guard],
            ['is_default' => false]
        );
        $staffPermissions = $allPermissions->filter(fn (Permission $p): bool => in_array($p->name, [
            'page_attendance_clock_page',
            'view_any_attendance_attendance',
            'create_attendance_attendance',
            'view_any_time_off_my::time::off',
            'view_time_off_my::time::off',
            'create_time_off_my::time::off',
            'update_time_off_my::time::off',
            'delete_time_off_my::time::off',
            'view_any_time_off_my::allocation',
            'view_time_off_my::allocation',
            'view_any_performance_performance::plan',
            'view_performance_performance::plan',
            'view_any_evaluation_evaluation',
            'view_evaluation_evaluation',
            'view_any_project_project',
            'view_project_project',
            'view_any_project_task',
            'view_project_task',
            'update_project_task',
            'view_any_project_timesheet',
            'create_project_timesheet',
            'update_project_timesheet',
        ], true));
        $staffRole->givePermissionTo($staffPermissions);
        $rolesSynced[] = $staffRole->name;

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return [
            'permissions_count' => $allPermissions->count(),
            'roles_synced'      => $rolesSynced,
        ];
    }
}
