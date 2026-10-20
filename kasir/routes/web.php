<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/index', [KasirController::class, 'index']);

Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');
