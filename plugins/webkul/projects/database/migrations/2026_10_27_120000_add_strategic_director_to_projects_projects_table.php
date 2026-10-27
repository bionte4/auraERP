<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects_projects')) {
            return;
        }

        if (! Schema::hasColumn('projects_projects', 'is_strategic')) {
            Schema::table('projects_projects', function (Blueprint $table): void {
                $table->boolean('is_strategic')->default(false);
            });
        }

        if (! Schema::hasColumn('projects_projects', 'project_director_id')) {
            Schema::table('projects_projects', function (Blueprint $table): void {
                $table->foreignId('project_director_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('projects_projects')) {
            return;
        }

        if (Schema::hasColumn('projects_projects', 'project_director_id')) {
            Schema::table('projects_projects', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('project_director_id');
            });
        }

        if (Schema::hasColumn('projects_projects', 'is_strategic')) {
            Schema::table('projects_projects', function (Blueprint $table): void {
                $table->dropColumn('is_strategic');
            });
        }
    }
};
