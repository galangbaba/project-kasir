<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kasir;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'portal')->name('portal');
Route::view('/lupa-sandi', 'lupa')->name('lupa');
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

// ---- Halaman login per peran (peran dikunci di server, bukan dari masukan form) ----
foreach ([
    'superadmin' => [User::SUPER_ADMIN, 'superadmin.login'],
    'tenant' => [User::ADMIN_TENANT, 'tenant.login'],
    'kasir' => [User::KASIR, 'kasir.login'],
] as $prefix => [$role, $name]) {
    Route::get("/$prefix/login", [LoginController::class, 'show'])->defaults('role', $role)->name($name);
    Route::post("/$prefix/login", [LoginController::class, 'login'])->defaults('role', $role)
        ->middleware('throttle:login')->name("$name.submit");
}

// ---- Super Admin ----
Route::prefix('superadmin')->name('superadmin.')->middleware('auth.session:' . User::SUPER_ADMIN)->group(function () {
    Route::get('/dashboard', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tenants', [SuperAdmin\TenantController::class, 'index'])->name('tenants');
    Route::get('/tenants/tambah', [SuperAdmin\TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [SuperAdmin\TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}/edit', [SuperAdmin\TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [SuperAdmin\TenantController::class, 'update'])->name('tenants.update');
    Route::post('/tenants/{tenant}/status', [SuperAdmin\TenantController::class, 'toggle'])->name('tenants.toggle');

    Route::get('/admin-tenant', [SuperAdmin\AdminTenantController::class, 'index'])->name('admin');
    Route::get('/admin-tenant/tambah', [SuperAdmin\AdminTenantController::class, 'create'])->name('admin.create');
    Route::post('/admin-tenant', [SuperAdmin\AdminTenantController::class, 'store'])->name('admin.store');
    Route::get('/admin-tenant/{admin}/edit', [SuperAdmin\AdminTenantController::class, 'edit'])->name('admin.edit');
    Route::put('/admin-tenant/{admin}', [SuperAdmin\AdminTenantController::class, 'update'])->name('admin.update');

    Route::get('/pengaturan', [SuperAdmin\SettingsController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/sandi', [SuperAdmin\SettingsController::class, 'password'])->name('pengaturan.sandi');
});

// ---- Admin Tenant (tenant_id selalu dari sesi, tidak ada parameter tenant di URL) ----
Route::prefix('tenant')->name('tenant.')->middleware('auth.session:' . User::ADMIN_TENANT)->group(function () {
    Route::get('/dashboard', [Tenant\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produk', [Tenant\ProductController::class, 'index'])->name('produk');
    Route::get('/produk/tambah', [Tenant\ProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [Tenant\ProductController::class, 'store'])->name('produk.store');
    Route::get('/produk/{product}/edit', [Tenant\ProductController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{product}', [Tenant\ProductController::class, 'update'])->name('produk.update');
    Route::post('/produk/{product}/stok', [Tenant\ProductController::class, 'stok'])->name('produk.stok');
    Route::post('/produk/{product}/status', [Tenant\ProductController::class, 'toggle'])->name('produk.toggle');

    Route::get('/pegawai', [Tenant\PegawaiController::class, 'index'])->name('pegawai');
    Route::get('/pegawai/tambah', [Tenant\PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/pegawai', [Tenant\PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{id}/edit', [Tenant\PegawaiController::class, 'edit'])->whereNumber('id')->name('pegawai.edit');
    Route::put('/pegawai/{id}', [Tenant\PegawaiController::class, 'update'])->whereNumber('id')->name('pegawai.update');
    Route::post('/pegawai/{id}/status', [Tenant\PegawaiController::class, 'toggle'])->whereNumber('id')->name('pegawai.toggle');

    Route::get('/laporan', [Tenant\ReportController::class, 'index'])->name('laporan');
    Route::get('/laporan/ekspor', [Tenant\ReportController::class, 'export'])->name('laporan.ekspor');
});

// ---- Kasir (mobile-first) ----
Route::prefix('kasir')->name('kasir.')->middleware('auth.session:' . User::KASIR)->group(function () {
    Route::get('/transaksi', [Kasir\TransaksiController::class, 'create'])->name('transaksi');
    Route::post('/transaksi', [Kasir\TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/struk/{transaksi}', [Kasir\TransaksiController::class, 'struk'])->name('struk');
    Route::post('/struk/{transaksi}/batal', [Kasir\TransaksiController::class, 'batal'])->name('batal');
    Route::get('/riwayat', [Kasir\TransaksiController::class, 'riwayat'])->name('riwayat');
});
