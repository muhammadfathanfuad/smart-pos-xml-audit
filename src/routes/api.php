<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/checkout', [OrderController::class, 'checkout']);
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/checkout', [OrderController::class, 'checkout']);
Route::get('/export-xml', [OrderController::class, 'exportXML']);
Route::post('/verify-xml', [OrderController::class, 'verify']);
Route::get('/download-snapshot/{id}', [OrderController::class, 'downloadSnapshot']);