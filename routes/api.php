<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::post('/sincronizar/produtos', [ProductController::class, 'sync']);
Route::post('/sincronizar/precos', [ProductController::class, 'syncPrices']);

Route::get('produtos-precos', [ProductController::class, 'index']);
