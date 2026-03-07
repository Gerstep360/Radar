<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportCollection;
use App\Http\Resources\UserResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * API ProfileController — Perfil de usuario (REST para Tauri).
 */
class ProfileController extends Controller
{
    /**
     * Perfil del usuario autenticado con sus denuncias.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->loadCount('reports');

        $recentReports = Report::where('user_id', $user->id)
            ->with(['category:id,name', 'media'])
            ->withCount(['votes', 'comments'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'stats' => [
                    'total_reports' => $user->reports_count,
                    'pending' => Report::where('user_id', $user->id)->where('status', 'pendiente')->count(),
                    'resolved' => Report::where('user_id', $user->id)->where('status', 'atendido')->count(),
                ],
                'recent_reports' => \App\Http\Resources\ReportResource::collection($recentReports),
            ],
        ]);
    }

    /**
     * Actualizar perfil (nombre, email).
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado.',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Cambiar contraseña.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.',
                'errors' => ['current_password' => ['La contraseña actual no coincide.']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Revocar todos los tokens excepto el actual
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada. Se cerraron las demás sesiones.',
        ]);
    }

    /**
     * Todas las denuncias del usuario (paginadas).
     */
    public function reports(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 50);

        $reports = Report::where('user_id', $request->user()->id)
            ->with(['category:id,name', 'media'])
            ->withCount(['votes', 'comments'])
            ->latest()
            ->paginate($perPage);

        return (new ReportCollection($reports))
            ->additional(['success' => true])
            ->response()
            ->setStatusCode(200);
    }
}
