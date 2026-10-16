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
            if (! Schema::hasColumn('projects_projects', 'operational_cost')) {
                $table->decimal('operational_cost', 15, 2)->nullable()->default(0)->after('project_value');
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
            if (Schema::hasColumn('projects_projects', 'operational_cost')) {
                $table->dropColumn('operational_cost');
            }
        });
    }
};
