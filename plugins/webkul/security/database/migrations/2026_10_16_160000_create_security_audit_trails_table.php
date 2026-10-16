<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('event', 64)->index();
            $table->string('description');
            $table->nullableMorphs('actor');
            $table->string('actor_label')->nullable();
            $table->nullableMorphs('subject');
            $table->string('subject_label')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('guard')->nullable();
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_trails');
    }
};
