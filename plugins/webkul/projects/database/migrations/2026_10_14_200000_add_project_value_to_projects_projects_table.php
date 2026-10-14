<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('projects_projects')) {
            return;
        }

        Schema::table('projects_projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects_projects', 'project_value')) {
                $table->decimal('project_value', 15, 2)->nullable()->default(0)->after('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('projects_projects')) {
            return;
        }

        Schema::table('projects_projects', function (Blueprint $table): void {
            if (Schema::hasColumn('projects_projects', 'project_value')) {
                $table->dropColumn('project_value');
            }
        });
    }
};
