<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(200);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('company_id');
        });

        Schema::table('attendances_attendances', function (Blueprint $table) {
            $table->foreignId('check_in_site_id')->nullable()->after('check_in_accuracy')->constrained('attendances_sites')->nullOnDelete();
            $table->foreignId('check_out_site_id')->nullable()->after('check_out_accuracy')->constrained('attendances_sites')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances_attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('check_in_site_id');
            $table->dropConstrainedForeignId('check_out_site_id');
        });

        Schema::dropIfExists('attendances_sites');
    }
};
