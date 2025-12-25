<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Notificaciones
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Listar notificaciones
    Route::get('/notificaciones', [NotificationController::class, 'index'])
        ->name('notifications.index');
    
    // Contador de no leídas
    Route::get('/notificaciones/sin-leer', [NotificationController::class, 'unreadCount'])
        ->name('notifications.unread');
    
    // Marcar una como leída
    Route::post('/notificaciones/{notification}/leer', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    
    // Marcar todas como leídas
    Route::post('/notificaciones/leer-todas', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});
