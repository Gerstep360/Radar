<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login — Retorna token Sanctum o solicita 2FA si está pendiente de verificación.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_id' => 'nullable|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Si el usuario tiene un código pendiente (recién registrado o reset), pedimos 2FA
        if ($user->push_2fa_code) {
             // Si el login ya trae el device_id, intentamos enviar el push si tiene tokens
             if ($user->fcmTokens()->exists()) {
                 $this->sendPushVerification($user, 'login', $request->input('device_name'));
             }
             
             return response()->json([
                'success' => true,
                'requires_2fa' => true,
                'message' => 'Tu cuenta requiere verificación. Se ha enviado un código a tus dispositivos vinculados.',
            ]);
        }

        return $this->respondWithToken($user, $request->input('device_name', 'tauri-app'));
    }

    /**
     * Registro — Crea usuario y genera código de verificación.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'device_name' => 'nullable|string|max:100',
            'fcm_token' => 'nullable|string',
            'device_id' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('ciudadano');
        }

        // Al registrarse, generamos el código de una vez
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'push_2fa_code' => $code,
            'push_2fa_expires_at' => now()->addMinutes(30), // Más tiempo para el primer login
        ]);

        // Si envió el token de una vez, lo guardamos y enviamos el push
        if ($request->fcm_token && $request->device_id) {
            \App\Models\FcmToken::create([
                'user_id' => $user->id,
                'token' => $request->fcm_token,
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'last_seen_at' => now(),
            ]);

            $user->notify(new \App\Notifications\SendFirebaseAlert(
                title: 'Bienvenido - Verifica tu cuenta',
                body: "Tu código de activación es: {$code}",
                data: ['type' => '2fa_code', 'code' => $code]
            ));
        }
        
        return response()->json([
            'success' => true,
            'requires_2fa' => true,
            'message' => 'Cuenta creada. Por favor, inicia sesión para recibir y verificar tu código.',
        ], 201);
    }

    /**
     * Verificar código 2FA enviado por Push.
     */
    public function verify2Fa(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->push_2fa_code !== $request->code || now()->gt($user->push_2fa_expires_at)) {
            return response()->json([
                'success' => false, 
                'message' => 'Código inválido o expirado.'
            ], 422);
        }

        // Limpiar código
        $user->update([
            'push_2fa_code' => null,
            'push_2fa_expires_at' => null,
        ]);

        return $this->respondWithToken($user, $request->input('device_name', 'tauri-app'));
    }

    /**
     * Generar y enviar código 2FA vía Firebase Push.
     */
    protected function sendPushVerification(User $user, string $type, ?string $deviceName = null): JsonResponse
    {
        // Solo generamos código si no tiene uno vigente o si es un nuevo login
        if (!$user->push_2fa_code || now()->gt($user->push_2fa_expires_at)) {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'push_2fa_code' => $code,
                'push_2fa_expires_at' => now()->addMinutes(10),
            ]);
        } else {
            $code = $user->push_2fa_code;
        }

        $origin = $deviceName ?: request()->ip();

        $user->notify(new \App\Notifications\SendFirebaseAlert(
            title: '🔐 Verificación de Seguridad',
            body: "Tu código de acceso es: {$code}",
            data: ['type' => '2fa_code', 'code' => $code, 'origin' => $origin]
        ));

        return response()->json([
            'success' => true,
            'requires_2fa' => true,
            'message' => 'Se ha enviado un código de verificación a tus dispositivos vinculados.',
        ]);
    }

    /**
     * Respuesta estandarizada con Token.
     */
    protected function respondWithToken(User $user, string $deviceName): JsonResponse
    {
        $deviceName = $deviceName ?: 'tauri-app';
        $user->tokens()->where('name', $deviceName)->delete();
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load('roles:id,name');
        
        $roleMessages = ['A' => 'Administrador', 'M' => 'Moderador', 'U' => 'Usuario Normal'];
        $roleName = $roleMessages[$user->role] ?? 'Usuario Normal';

        return response()->json([
            'success' => true,
            'message' => "Inicio de sesión exitoso como {$roleName}.",
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    /**
     * Usuario autenticado.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('roles:id,name');
        $user->loadCount('reports');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    /**
     * Olvidé mi contraseña — Envía código al dispositivo más antiguo (OG).
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
        }

        // Buscar el dispositivo "más antiguo" (OG)
        $ogToken = $user->fcmTokens()->orderBy('created_at', 'asc')->first();

        if (!$ogToken) {
            return response()->json([
                'success' => false, 
                'message' => 'No tienes dispositivos registrados para recuperar la contraseña. Contacta a soporte.'
            ], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'push_reset_code' => $code,
            'push_reset_expires_at' => now()->addMinutes(15),
        ]);

        // Notificar solo al dispositivo OG
        try {
            $messaging = app(\Kreait\Firebase\Contract\Messaging::class);
            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $ogToken->token)
                ->withNotification(\Kreait\Firebase\Messaging\Notification::create(
                    'Recuperación de Contraseña',
                    "Código de recuperación: {$code}. No lo compartas."
                ))
                ->withData(['type' => 'password_reset', 'code' => $code]);

            $messaging->send($message);
        } catch (\Exception $e) {
            \Log::error("Error enviando recuperación Push: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Se ha enviado un código de recuperación a tu dispositivo principal ({$ogToken->device_name}).",
        ]);
    }

    /**
     * Resetear contraseña con el código recibido.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->push_reset_code !== $request->code || now()->gt($user->push_reset_expires_at)) {
            return response()->json(['success' => false, 'message' => 'Código inválido o expirado.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'push_reset_code' => null,
            'push_reset_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.',
        ]);
    }

    /**
     * Logout — Revocar token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada.',
        ]);
    }
}
