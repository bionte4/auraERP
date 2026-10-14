<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees_employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees_employees', 'employment_type_id')) {
                $table->unsignedBigInteger('employment_type_id')->nullable();
                $table->foreign('employment_type_id')
                    ->references('id')
                    ->on('employees_employment_types')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('employees_employees', 'join_date')) {
                $table->date('join_date')->nullable();
            }

            if (! Schema::hasColumn('employees_employees', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable();
            }
        });

        if (Schema::hasTable('employees_employment_types')) {
            $types = [
                ['name' => 'Permanent', 'code' => 'Permanent', 'sort' => 1],
                ['name' => 'Contract', 'code' => 'Contract', 'sort' => 2],
                ['name' => 'Konsultan', 'code' => 'Consultant', 'sort' => 3],
                ['name' => 'Freelance', 'code' => 'Freelance', 'sort' => 4],
            ];

            foreach ($types as $type) {
                $exists = DB::table('employees_employment_types')
                    ->whereRaw('LOWER(name) = ?', [strtolower($type['name'])])
                    ->exists();

                if (! $exists) {
                    DB::table('employees_employment_types')->insert([
                        'name'       => $type['name'],
                        'code'       => $type['code'],
                        'sort'       => $type['sort'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('employees_employees', function (Blueprint $table): void {
            if (Schema::hasColumn('employees_employees', 'employment_type_id')) {
                $table->dropForeign(['employment_type_id']);
                $table->dropColumn('employment_type_id');
            }

            if (Schema::hasColumn('employees_employees', 'join_date')) {
                $table->dropColumn('join_date');
            }

            if (Schema::hasColumn('employees_employees', 'contract_end_date')) {
                $table->dropColumn('contract_end_date');
            }
        });
    }
};
