<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\CardPurchaseController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/movements/history', [MovementController::class, 'history'])->name('movements.history');
    Route::get('/credits/history', [CreditController::class, 'history'])->name('credits.history');

    // Rutas para Cuentas
    Route::resource('accounts', AccountController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

    // Rutas para Categorías
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::post('/categories/create-defaults', [CategoryController::class, 'createDefaults'])->name('categories.createDefaults');

    // Rutas para Movimientos
    Route::resource('movements', MovementController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

    //Rutas para Tarjetas de Credito
    Route::resource('credit-cards', CreditCardController::class)->only(['index', 'store', 'show']);
    Route::post('/credit-cards/{credit_card}/purchases', [CardPurchaseController::class, 'store'])->name('card-purchases.store');

    // Rutas para Créditos
    Route::resource('credits', CreditController::class)->only(['index', 'store', 'show']);
    Route::post('/credits/{credit}/pay', [CreditController::class, 'pay'])->name('credits.pay');

    // Rutas para Préstamos
    Route::resource('loans', LoanController::class)->only(['index', 'store']);

    // NUEVA RUTA PARA REGISTRAR UN ABONO
    Route::post('/loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');

});

require __DIR__ . '/auth.php';
