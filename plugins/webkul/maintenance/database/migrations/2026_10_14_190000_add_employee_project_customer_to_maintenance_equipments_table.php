<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('maintenance_equipments')) {
            return;
        }

        Schema::table('maintenance_equipments', function (Blueprint $table): void {
            if (! Schema::hasColumn('maintenance_equipments', 'employee_id')) {
                $column = $table->foreignId('employee_id')->nullable()->after('owner_user_id');

                if (Schema::hasTable('employees_employees')) {
                    $column->constrained('employees_employees')->nullOnDelete();
                }
            }

            if (! Schema::hasColumn('maintenance_equipments', 'project_id')) {
                $column = $table->foreignId('project_id')->nullable()->after('employee_id');

                if (Schema::hasTable('projects_projects')) {
                    $column->constrained('projects_projects')->nullOnDelete();
                }
            }

            if (! Schema::hasColumn('maintenance_equipments', 'customer_id')) {
                $column = $table->foreignId('customer_id')->nullable()->after('project_id');

                if (Schema::hasTable('partners_partners')) {
                    $column->constrained('partners_partners')->nullOnDelete();
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('maintenance_equipments')) {
            return;
        }

        Schema::table('maintenance_equipments', function (Blueprint $table): void {
            foreach (['customer_id', 'project_id', 'employee_id'] as $col) {
                if (Schema::hasColumn('maintenance_equipments', $col)) {
                    $table->dropConstrainedForeignId($col);
                }
            }
        });
    }
};
