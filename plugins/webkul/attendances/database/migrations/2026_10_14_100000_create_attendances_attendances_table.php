<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('employee_id')->constrained('employees_employees')->cascadeOnDelete();
            $table->date('work_date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'work_date']);
            $table->index('company_id');
            $table->index('work_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances_attendances');
    }
};
