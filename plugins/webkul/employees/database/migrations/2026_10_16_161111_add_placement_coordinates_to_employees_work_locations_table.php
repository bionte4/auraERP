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
        Schema::table('employees_work_locations', function (Blueprint $table) {
            $table->string('city')->nullable()->after('location_number')->comment('City for placement map');
            $table->decimal('latitude', 10, 7)->nullable()->after('city')->comment('Placement latitude');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude')->comment('Placement longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees_work_locations', function (Blueprint $table) {
            $table->dropColumn(['city', 'latitude', 'longitude']);
        });
    }
};
