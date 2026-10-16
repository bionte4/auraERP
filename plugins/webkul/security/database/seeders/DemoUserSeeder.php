<?php

namespace Webkul\Security\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;
use Webkul\Support\Models\Company;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->first();

        if ($company === null) {
            $this->command?->error('No company found. Run erp:install before seeding demo users.');

            return;
        }

        $hrRole = $this->roleWithPermissions('HRD', $this->permissionNames([
            'employee',
            'attendance',
            'time_off',
            'performance',
            'evaluation',
        ]));

        $staffRole = $this->roleWithPermissions('Karyawan', $this->permissionNames(['attendance'])
            ->reject(fn (string $name): bool => str_contains($name, 'delete')));

        app(RolePermissionSynchronizer::class)->syncAll(false);

        $accountingRole = Role::query()->firstOrCreate([
            'name'       => 'Accounting',
            'guard_name' => 'web',
        ]);

        $hrUser = $this->user('HRD Demo', 'hrd@opsora.my.id', 'Demo-hrd-2026', $company->id, $hrRole);
        $staffUser = $this->user('Karyawan Demo', 'karyawan@opsora.my.id', 'Demo-karyawan-2026', $company->id, $staffRole);
        $this->user('Accounting Demo', 'accounting@opsora.my.id', 'Demo-accounting-2026', $company->id, $accountingRole);

        $this->linkStaffToEmployee($staffUser);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Demo HRD: hrd@opsora.my.id / Demo-hrd-2026');
        $this->command?->info('Demo karyawan: karyawan@opsora.my.id / Demo-karyawan-2026');
        $this->command?->info('Demo Accounting: accounting@opsora.my.id / Demo-accounting-2026');
    }

    /**
     * @param  array<int, string>  $markers
     * @return Collection<int, string>
     */
    protected function permissionNames(array $markers): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->filter(function (string $name) use ($markers): bool {
                foreach ($markers as $marker) {
                    if (str_contains($name, $marker)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    /**
     * @param  Collection<int, string>  $permissionNames
     */
    protected function roleWithPermissions(string $name, Collection $permissionNames): Role
    {
        $role = Role::query()->firstOrCreate([
            'name'       => $name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($permissionNames->all());

        return $role;
    }

    protected function user(string $name, string $email, string $password, int $companyId, Role $role): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name'                 => $name,
                'password'             => $password,
                'is_active'            => true,
                'language'             => 'id',
                'resource_permission'  => PermissionType::GLOBAL,
                'default_company_id'   => $companyId,
            ],
        );

        $user->syncRoles([$role]);
        $user->allowedCompanies()->syncWithoutDetaching([$companyId]);

        return $user;
    }

    protected function linkStaffToEmployee(User $user): void
    {
        if (! Schema::hasTable('employees_employees')) {
            return;
        }

        $alreadyLinked = Employee::withoutGlobalScopes()->where('user_id', $user->id)->exists();

        if ($alreadyLinked) {
            return;
        }

        $employee = Employee::withoutGlobalScopes()->whereNull('user_id')->first();

        if ($employee === null) {
            return;
        }

        $employee->forceFill(['user_id' => $user->id])->save();
    }
}
