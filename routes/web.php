<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SMART-LOUMA — Web Routes (Admin Dashboard Laravel)
|--------------------------------------------------------------------------
*/

// ── Page d'accueil → Marketplace (index.html) ───────────────────────────
Route::get('/', function () {
    $path = public_path('index.html');
    if (!file_exists($path)) {
        $path = base_path('index.html');
    }
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'text/html']);
    }
    return redirect('/admin');
});

// ── Auth admin ─────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Connexion
    Route::get('login',   [AdminController::class, 'loginForm'])->name('login');
    Route::post('login',  [AdminController::class, 'login'])->name('login.post');
    Route::post('logout', [AdminController::class, 'logout'])->name('logout');

    // Dashboard (protégé dans le controller par middleware)
    Route::get('/',                              [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users',                          [AdminController::class, 'users'])->name('users');
    Route::get('products',                       [AdminController::class, 'products'])->name('products');
    Route::get('orders',                         [AdminController::class, 'orders'])->name('orders');
    Route::get('contacts',                       [AdminController::class, 'contacts'])->name('contacts');
    Route::get('contacts/{message}',             [AdminController::class, 'showContact'])->name('contacts.show');
    Route::get('producers',                      [AdminController::class, 'producers'])->name('producers');

    // Actions
    Route::post('producers/{user}/approve',      [AdminController::class, 'approveProducer'])->name('producers.approve');
    Route::post('users/{user}/suspend',          [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::post('users/{user}/reactivate',       [AdminController::class, 'reactivateUser'])->name('users.reactivate');
    Route::delete('users/{user}',                [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('orders/{order}/status',         [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('products/{product}/toggle',     [AdminController::class, 'toggleProduct'])->name('products.toggle');
    Route::delete('products/{product}',          [AdminController::class, 'deleteProduct'])->name('products.delete');
});
