<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;

Route::get('/teste', function () {
    return 'API OK';
});

Route::middleware(['api.token'])->group(function () {
    Route::post('/liberar-conta', [UsuarioController::class, 'liberarConta']);
    Route::post('/suspender-conta', [UsuarioController::class, 'suspenderConta']);
});