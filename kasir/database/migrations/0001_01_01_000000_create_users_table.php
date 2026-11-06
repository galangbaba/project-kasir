<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tenants (ERD: Tenants) — satu unit usaha / client
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tenants', 100);
            $table->string('kategori_usaha', 60)->nullable();
            $table->string('penanggung_jawab', 100);
            $table->string('status', 15)->default('aktif'); // aktif|nonaktif
            $table->unsignedInteger('trx_counter')->default(0); // penomoran TRX per tenant
            $table->timestamps();
            $table->index('status');
        });

        // User (ERD: User) — super_admin | admin_tenant | kasir
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->restrictOnDelete();
            $table->string('nama', 100);
            $table->string('email', 150)->nullable()->unique();
            $table->string('username', 50)->unique();
            $table->string('password', 64); // MD5 (32 hex) — sesuai permintaan
            $table->string('role', 20);     // super_admin|admin_tenant|kasir
            $table->string('status', 15)->default('aktif');
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'role']);
        });

        // Tabel sesi bawaan Laravel (CSRF + flash)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('tenants');
    }
};
