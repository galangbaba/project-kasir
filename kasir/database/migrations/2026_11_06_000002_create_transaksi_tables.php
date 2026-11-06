<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // transaksi (ERD: transaksi)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // kasir
            $table->string('no_transaksi', 20);
            $table->unsignedInteger('total_bayar');
            $table->string('metode_bayar', 15); // tunai|transfer
            $table->string('status', 15)->default('selesai'); // selesai|dibatalkan
            $table->timestamp('dibatalkan_at')->nullable();
            $table->foreignId('dibatalkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id', 'no_transaksi']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'user_id', 'created_at']);
        });

        // snapshot harga
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('nama_product', 100); //
            $table->unsignedInteger('harga_satuan'); // 
            $table->unsignedInteger('jumlah');
            $table->unsignedInteger('subtotal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
    }
};
