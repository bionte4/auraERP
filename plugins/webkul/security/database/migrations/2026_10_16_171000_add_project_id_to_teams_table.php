<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teams') || Schema::hasColumn('teams', 'project_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id')->nullable()->after('name')->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('teams') || ! Schema::hasColumn('teams', 'project_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table): void {
            $table->dropColumn('project_id');
        });
    }
};
