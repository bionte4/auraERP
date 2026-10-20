<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects_projects', function (Blueprint $table) {
            $table->foreignId('team_leader_employee_id')
                ->nullable()
                ->after('user_id')
                ->constrained('employees_employees')
                ->nullOnDelete();
        });

        // One team leader may only lead one active project at a time.
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                CREATE UNIQUE INDEX projects_projects_one_active_project_per_team_leader
                ON projects_projects (team_leader_employee_id)
                WHERE team_leader_employee_id IS NOT NULL AND is_active = true AND deleted_at IS NULL
            SQL);
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS projects_projects_one_active_project_per_team_leader');
        }

        Schema::table('projects_projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_leader_employee_id');
        });
    }
};
