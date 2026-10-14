<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations_evaluations')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('weight')->default(0);
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations_ratings');
    }
};
