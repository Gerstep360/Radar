<?php
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Votar/quitar voto de un reporte
    Route::post('/reports/{report}/vote', [VoteController::class, 'toggle'])
        ->name('reports.vote');
});
