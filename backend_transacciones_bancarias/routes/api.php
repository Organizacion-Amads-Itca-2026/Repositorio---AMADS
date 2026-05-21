<?php

use App\Http\Controllers\Api\AutorizadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/autorizar', [AutorizadorController::class, 'autorizarTransaccion']);
Route::get('/hola', [AutorizadorController::class, 'prueba']);
