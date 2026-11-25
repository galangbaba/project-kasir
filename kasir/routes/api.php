
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductApiController;

// TEST API
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API KasirKu berhasil dijalankan!'
    ]);
});

// AUTHENTICATION
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ROUTE YANG MEMBUTUHKAN LOGIN
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // PRODUCT CRUD
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::put('/products/{id}', [ProductApiController::class, 'update']);
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);

});
