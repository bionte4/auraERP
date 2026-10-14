<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('maintenance_equipment_movements')) {
            return;
        }

        Schema::create('maintenance_equipment_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('equipment_id')
                ->constrained('maintenance_equipments')
                ->cascadeOnDelete();

            $table->string('movement_type')->default('relocation');
            $table->date('movement_date');
            $table->string('reference')->nullable();

            $table->foreignId('from_employee_id')->nullable();
            $table->foreignId('to_employee_id')->nullable();

            $table->foreignId('from_project_id')->nullable();
            $table->foreignId('to_project_id')->nullable();

            $table->foreignId('from_customer_id')->nullable();
            $table->foreignId('to_customer_id')->nullable();

            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();

            $table->foreignId('replacement_equipment_id')
                ->nullable()
                ->constrained('maintenance_equipments')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_equipment_movements');
    }
};
