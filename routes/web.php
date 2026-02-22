<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('/sincronizar/produtos', [ProductController::class, 'sync']); //TODO: Ajustar a rota para POST
    Route::get('/sincronizar/precos', [ProductController::class, 'syncPrices']); //TODO: Ajustar a rota para POST

    Route::get('produtos-precos', [ProductController::class, 'index']);
});
