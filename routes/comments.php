<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Comentarios
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Obtener comentarios de un reporte
    Route::get('/reportes/{report}/comentarios', [CommentController::class, 'index'])
        ->name('comments.index');
    
    // Agregar comentario
    Route::post('/reportes/{report}/comentarios', [CommentController::class, 'store'])
        ->name('comments.store');
    
    // Eliminar comentario
    Route::delete('/comentarios/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});
