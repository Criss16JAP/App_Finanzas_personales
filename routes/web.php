<?php

// routes/web.php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    // ... (otras rutas protegidas como el dashboard)

    // Rutas para el Módulo de Cuentas
    Route::resource('accounts', AccountController::class)->only([
        'index', 'store', 'edit', 'update', 'destroy'
    ]);
});

require __DIR__.'/auth.php';
