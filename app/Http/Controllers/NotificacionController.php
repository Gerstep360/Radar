<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificacionController extends Controller
{
    public function enviarPush(Messaging $messaging)
    {
        // Este token es el identificador único del teléfono del usuario.
        // Tu app en Tauri debe generarlo y enviarlo a tu base de datos previamente.
        $deviceToken = 'token_del_telefono_generado_por_la_app_tauri';

        // 1. Creas la parte visual de la notificación
        $notificacion = Notification::create(
            '¡Hola líder!', 
            'Esta es una prueba de notificación desde mi VPS con Laravel.'
        );

        // 2. Armas el paquete completo (puedes enviar datos ocultos también)
        $mensaje = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notificacion)
            ->withData([
                'accion' => 'abrir_pantalla_perfil',
                'id_usuario' => '123'
            ]);

        // 3. Disparas el misil
        try {
            $messaging->send($mensaje);
            return response()->json(['mensaje' => 'Notificación enviada con éxito a los servidores de Google']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falló el envío: ' . $e->getMessage()], 500);
        }
    }
}
