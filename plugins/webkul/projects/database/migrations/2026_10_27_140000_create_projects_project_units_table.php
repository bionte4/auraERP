<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects_project_units') || ! Schema::hasTable('projects_projects')) {
            return;
        }

        Schema::create('projects_project_units', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects_projects')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('mos_employee_id');
            $table->unsignedBigInteger('team_leader_employee_id');
            $table->timestamps();

            $table->unique(['project_id', 'department_id']);
            $table->index('mos_employee_id');
            $table->index('team_leader_employee_id');
        });

        if (Schema::hasTable('employees_departments')) {
            Schema::table('projects_project_units', function (Blueprint $table): void {
                $table->foreign('department_id')
                    ->references('id')
                    ->on('employees_departments')
                    ->restrictOnDelete();
            });
        }

        if (Schema::hasTable('employees_employees')) {
            Schema::table('projects_project_units', function (Blueprint $table): void {
                $table->foreign('mos_employee_id')
                    ->references('id')
                    ->on('employees_employees')
                    ->restrictOnDelete();
                $table->foreign('team_leader_employee_id')
                    ->references('id')
                    ->on('employees_employees')
                    ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('projects_project_units');
    }
};
