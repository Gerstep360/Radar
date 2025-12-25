<?php
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    // CRUD de Denuncias
    Route::resource('denuncias', DenunciaController::class)
        ->only(['index', 'create', 'store', 'show', 'update'])
        ->whereNumber('denuncia')
        ->missing(function () {
            return redirect()->route('denuncias.index')
                ->with('error', 'Esa denuncia no existe en el radar.');
        });

    // Cambiar estado (admin/funcionarios)
    Route::patch('/denuncias/{denuncia}/estado', [DenunciaController::class, 'updateStatus'])
        ->name('denuncias.status')
        ->whereNumber('denuncia')
        ->middleware('can:cambiarEstado,denuncia');
});