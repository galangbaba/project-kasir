<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/index', [KasirController::class, 'index']);

Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');

Route::get('/kasir', [KasirController::class, 'kasir'])->name('kasir.kasir');

Route::get('/menu', [KasirController::class, 'menu'])->name('menu.menu');

Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('transaksi.transaksi');
