<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('employee_id')->constrained('employees_employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->string('title');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft');
            $table->text('summary')->nullable();
            $table->text('employee_comment')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index(['employee_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations_evaluations');
    }
};
