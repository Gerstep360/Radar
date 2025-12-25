<?php

use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Map Routes
|--------------------------------------------------------------------------
|
| Rutas para operaciones del mapa de denuncias.
|
*/

Route::middleware(['auth'])->prefix('mapa')->name('map.')->group(function () {
    Route::get('/puntos', [MapController::class, 'points'])->name('points');
    Route::get('/puntos/{id}', [MapController::class, 'point'])->name('point');
    Route::get('/area', [MapController::class, 'pointsInBounds'])->name('bounds');
    Route::get('/clusters', [MapController::class, 'clusters'])->name('clusters');
    Route::get('/tiempo-real', [MapController::class, 'realtimeConfig'])->name('realtime');
    Route::post('/limpiar-cache', [MapController::class, 'invalidateCache'])->name('cache.clear');
});
