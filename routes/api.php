<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SMART-LOUMA — API Routes
| Préfixe : /api
|--------------------------------------------------------------------------
*/

// ── Auth (public) ──────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// ── Stats publiques ────────────────────────────────────────────────────
Route::get('stats', [StatsController::class, 'public']);

// ── Produits (liste publique) ──────────────────────────────────────────
Route::get('products',      [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

// ── Contact (public) ──────────────────────────────────────────────────
Route::post('contact', [ContactController::class, 'store']);

// ── Routes authentifiées (Sanctum) ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Produits (CRUD protégé)
    Route::post('products',                        [ProductController::class, 'store']);
    Route::put('products/{product}',               [ProductController::class, 'update']);
    Route::delete('products/{product}',            [ProductController::class, 'destroy']);
    Route::patch('products/{product}/toggle',      [ProductController::class, 'toggleAvailability']);
    Route::get('my-products',                      [ProductController::class, 'myProducts']);

    // Commandes
    Route::post('orders',                          [OrderController::class, 'store']);
    Route::get('my-orders',                        [OrderController::class, 'myOrders']);

    // Admin uniquement
    Route::middleware('can:admin')->group(function () {
        Route::get('orders',                       [OrderController::class, 'index']);
        Route::patch('orders/{order}/status',      [OrderController::class, 'updateStatus']);
        Route::get('stats/admin',                  [StatsController::class, 'admin']);
    });
});
