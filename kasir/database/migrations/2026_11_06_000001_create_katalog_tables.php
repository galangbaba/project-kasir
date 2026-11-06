<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories (ERD: Categories)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('nama_kategori', 60);
            $table->timestamps();
            $table->unique(['tenant_id', 'nama_kategori']);
        });

        // Product (ERD: Product) — nilai uang integer, stok tidak boleh negatif
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('nama_product', 100);
            $table->unsignedInteger('harga');
            $table->unsignedInteger('stok')->default(0);
            $table->boolean('is_active')->default(true); // soft delete (nonaktif)
            $table->timestamps();
            $table->index(['tenant_id', 'is_active']);
        });

        // stock_logs (SRS: riwayat perubahan stok, append-only)
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipe', 20); // awal|restock|koreksi|penjualan|void
            $table->integer('perubahan');
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            $table->string('keterangan', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
