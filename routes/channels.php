<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels - Radar Real-Time
|--------------------------------------------------------------------------
*/

// Canal privado para el usuario (notificaciones personales)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal público del radar - todos pueden escuchar
// No requiere autenticación
Broadcast::channel('radar', function () {
    return true;
});

// Canal de un reporte específico (para comentarios)
Broadcast::channel('report.{reportId}', function ($user, $reportId) {
    // Cualquier usuario autenticado puede escuchar
    return $user !== null;
});
