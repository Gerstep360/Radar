<?php

use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vote Routes
|--------------------------------------------------------------------------
|
| Rutas para gestión de votos en denuncias.
|
*/

Route::middleware(['auth'])->prefix('denuncias')->group(function () {
    Route::post('/{report}/votar', [VoteController::class, 'toggle'])->name('denuncias.vote');
    Route::get('/{report}/voto', [VoteController::class, 'check'])->name('denuncias.vote.check');
});
