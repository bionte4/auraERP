<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects_project_employees')) {
            return;
        }

        Schema::create('projects_project_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('projects_projects')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'employee_id']);
            $table->index('employee_id');
        });

        if (Schema::hasTable('employees_employees')) {
            Schema::table('projects_project_employees', function (Blueprint $table) {
                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees_employees')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('projects_project_employees');
    }
};
