<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounts_account_moves') || Schema::hasColumn('accounts_account_moves', 'project_id')) {
            return;
        }

        Schema::table('accounts_account_moves', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id')->nullable()->index();

            // The projects plugin may not be installed yet; only add
            // the foreign key when its table is actually there.
            if (Schema::hasTable('projects_projects')) {
                $table->foreign('project_id')
                    ->references('id')
                    ->on('projects_projects')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounts_account_moves') || ! Schema::hasColumn('accounts_account_moves', 'project_id')) {
            return;
        }

        Schema::table('accounts_account_moves', function (Blueprint $table): void {
            $table->dropColumn('project_id');
        });
    }
};
