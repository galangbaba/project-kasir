<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesi login berbasis token opaque (Bab 6): hanya HASH token yang disimpan.
        Schema::create('auth_sessions', function (Blueprint $table) {
            $table->ulid('id')->primary(); // pengenal tidak berurutan
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->binary('token_hash', 32)->unique();
            $table->string('status', 15)->default('active'); // active|revoked|expired
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        // Audit log append-only (NFR-AUD-01)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('aksi', 50);
            $table->string('entitas', 50)->nullable();
            $table->unsignedBigInteger('entitas_id')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('auth_sessions');
    }
};
