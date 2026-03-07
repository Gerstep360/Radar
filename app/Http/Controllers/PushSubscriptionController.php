<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FcmToken;

class PushSubscriptionController extends Controller
{
    /**
     * Update user's FCM token (replacing old WebPush subscribe).
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'fcm_token'   => 'required|string',
            'device_id'   => 'required|string', // Ahora obligatorio para rastrear 5 dispositivos por cuenta
            'device_name' => 'nullable|string',
        ]);

        $user = $request->user();
        $token = $request->input('fcm_token');
        $deviceId = $request->input('device_id');
        $deviceName = $request->input('device_name');

        // Actualizar o crear el token para este dispositivo específico del usuario
        FcmToken::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $deviceId],
            [
                'token' => $token, 
                'device_name' => $deviceName,
                'last_seen_at' => now(), // Rastrear actividad
            ]
        );

        return response()->json(['success' => true, 'message' => 'Token FCM registrado y dispositivo rastreado'], 200);
    }

    /**
     * Delete user's FCM token (replacing old WebPush unsubscribe).
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $token = $request->input('fcm_token');

        // Eliminar el token específico (el cast 'encrypted' manejará la búsqueda si comparamos el valor, 
        // pero es mejor borrar por ID o simplemente borrar todos los tokens de este usuario si es logout total.
        // Aquí borramos el token que coincida con el usuario actual).
        $user->fcmTokens()->where('token', $token)->delete();

        return response()->json(['success' => true, 'message' => 'Token FCM eliminado'], 200);
    }
}
