<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_plan_id')->constrained('performance_plans')->cascadeOnDelete();
            $table->string('title');
            $table->string('metric')->nullable();
            $table->string('target')->nullable();
            $table->unsignedTinyInteger('weight')->default(0);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('status')->default('not_started');
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_goals');
    }
};
