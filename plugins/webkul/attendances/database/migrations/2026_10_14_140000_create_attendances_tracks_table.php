<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances_tracks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('attendance_id')->constrained('attendances_attendances')->cascadeOnDelete();
            $table->dateTime('recorded_at');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('accuracy')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index(['attendance_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances_tracks');
    }
};
