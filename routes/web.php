<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;

Route::get('/', [FrontController::class, 'index']);
Route::get('/produk', [FrontController::class, 'index']);
