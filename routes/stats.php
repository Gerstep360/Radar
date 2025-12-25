<?php

use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stats Routes
|--------------------------------------------------------------------------
|
| Rutas para estadísticas y métricas del dashboard.
|
*/

Route::middleware(['auth'])->prefix('estadisticas')->name('stats.')->group(function () {
    Route::get('/dashboard', [StatsController::class, 'dashboard'])->name('dashboard');
    Route::get('/usuarios-activos', [StatsController::class, 'topUsuarios'])->name('top-users');
    Route::get('/tendencias', [StatsController::class, 'trending'])->name('trending');
    Route::post('/limpiar-cache', [StatsController::class, 'invalidateCache'])->name('cache.clear');
});
