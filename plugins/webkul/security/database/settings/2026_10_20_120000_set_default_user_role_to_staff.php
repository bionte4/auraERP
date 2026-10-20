<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Least-privilege default: newly invited/created users must start as
     * Staff and be promoted manually once their placement is clear,
     * instead of inheriting a broad role (e.g. Operation Manager).
     */
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        $staffRoleId = DB::table('roles')
            ->where('name', 'Staff')
            ->value('id');

        if (! $staffRoleId) {
            return;
        }

        $this->migrator->update(
            'general.default_role_id',
            fn ($current) => (int) $staffRoleId,
        );
    }
};
