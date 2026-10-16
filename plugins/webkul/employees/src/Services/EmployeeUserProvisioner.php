<?php

namespace Webkul\Employee\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;

class EmployeeUserProvisioner
{
    /**
     * Create (or reuse) a login user for an employee and link Related User.
     *
     * @return array{user: User, created: bool, password: ?string}
     */
    public function provision(Employee $employee): array
    {
        if ($employee->user_id) {
            $existing = User::query()->find($employee->user_id);

            if ($existing instanceof User) {
                return ['user' => $existing, 'created' => false, 'password' => null];
            }
        }

        $email = trim((string) ($employee->work_email ?: $employee->private_email ?: ''));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(__('employees::filament/resources/employee/pages/manage-overview.provision.errors.email'));
        }

        $existingByEmail = User::query()
            ->whereRaw(db_dialect()->caseInsensitiveEquals('email'), [$email])
            ->first();

        if ($existingByEmail instanceof User) {
            if ($existingByEmail->employee()->where('id', '!=', $employee->id)->exists()) {
                throw new RuntimeException(__('employees::filament/resources/employee/pages/manage-overview.provision.errors.email-taken'));
            }

            $employee->forceFill(['user_id' => $existingByEmail->id])->save();

            return ['user' => $existingByEmail, 'created' => false, 'password' => null];
        }

        $plainPassword = 'Aura-'.Str::password(10);

        $user = User::query()->create([
            'name'       => $employee->name,
            'email'      => $email,
            'password'   => Hash::make($plainPassword),
            'is_active'  => true,
            'partner_id' => $employee->partner_id,
        ]);

        foreach (['web', 'sanctum'] as $guard) {
            Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => $guard]);
        }

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Staff');
        }

        $employee->forceFill(['user_id' => $user->id])->save();

        return ['user' => $user, 'created' => true, 'password' => $plainPassword];
    }

    /**
     * @return array{provisioned: int, skipped: int, errors: array<int, string>}
     */
    public function provisionMissingForAssignedEmployees(): array
    {
        $employees = Employee::query()
            ->whereNull('user_id')
            ->whereHas('projects')
            ->orderBy('name')
            ->get();

        $provisioned = 0;
        $skipped = 0;
        $errors = [];

        foreach ($employees as $employee) {
            try {
                $result = $this->provision($employee);
                $result['created'] || $result['user'] ? $provisioned++ : $skipped++;
            } catch (RuntimeException $exception) {
                $skipped++;
                $errors[$employee->id] = $employee->name.': '.$exception->getMessage();
            }
        }

        return compact('provisioned', 'skipped', 'errors');
    }
}
