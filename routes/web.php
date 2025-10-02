<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Cuentas
    Route::resource('accounts', AccountController::class)->only([
        'index', 'store', 'edit', 'update', 'destroy'
    ]);

    // Rutas para Categorías
    Route::resource('categories', CategoryController::class)->only([
        'index', 'store', 'edit', 'update', 'destroy'
    ]);

    // Rutas para Movimientos
    Route::resource('movements', MovementController::class)->only([
        'index', 'store'
    ]);

    // Rutas para Préstamos (NUEVA LÍNEA)
    Route::resource('loans', LoanController::class)->only(['index', 'store']);

    // NUEVA RUTA PARA REGISTRAR UN ABONO
    Route::post('/loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');
});

require __DIR__.'/auth.php';
