<?php

namespace Webkul\Project\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class ProjectStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $stages = [
            ['name' => 'To Do', 'sort' => 1],
            ['name' => 'In Progress', 'sort' => 2],
            ['name' => 'Done', 'sort' => 3],
            ['name' => 'Cancelled', 'sort' => 4],
        ];

        foreach ($stages as $stage) {
            $existing = DB::table('projects_project_stages')->where('name', $stage['name'])->first();

            if ($existing) {
                DB::table('projects_project_stages')
                    ->where('id', $existing->id)
                    ->update([
                        'is_active'  => true,
                        'sort'       => $stage['sort'],
                        'updated_at' => now(),
                    ]);

                continue;
            }

            DB::table('projects_project_stages')->insert([
                'name'       => $stage['name'],
                'is_active'  => true,
                'sort'       => $stage['sort'],
                'creator_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
