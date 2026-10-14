<?php

namespace Webkul\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees_employment_types')->delete();

        $user = User::first();

        $employmentTypes = [
            ['creator_id' => $user?->id, 'sort' => 1, 'name' => 'Permanent', 'code' => 'Permanent', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 2, 'name' => 'Contract', 'code' => 'Contract', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 3, 'name' => 'Konsultan', 'code' => 'Consultant', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 4, 'name' => 'Freelance', 'code' => 'Freelance', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 5, 'name' => 'Temporary', 'code' => 'Temporary', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 6, 'name' => 'Seasonal', 'code' => 'Seasonal', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 7, 'name' => 'Interim', 'code' => 'Interim', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 8, 'name' => 'Full-Time', 'code' => 'Full-Time', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 9, 'name' => 'Intern', 'code' => 'Intern', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 10, 'name' => 'Student', 'code' => 'Student', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 11, 'name' => 'Apprenticeship', 'code' => 'Apprenticeship', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 12, 'name' => 'Thesis', 'code' => 'Thesis', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 13, 'name' => 'Statutory', 'code' => 'Statutory', 'created_at' => now(), 'updated_at' => now()],
            ['creator_id' => $user?->id, 'sort' => 14, 'name' => 'Employee', 'code' => 'Employee', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('employees_employment_types')->insert($employmentTypes);
    }
}
