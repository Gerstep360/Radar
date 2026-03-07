<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

/**
 * API AppUpdateController — Consultar actualizaciones de la App (REST para Tauri).
 */
class AppUpdateController extends Controller
{
    /**
     * Devuelve la información de la última actualización activa.
     */
    public function latestVersion(): JsonResponse
    {
        try {
            $latest = AppUpdate::where('is_active', true)
                ->orderBy('id', 'desc')
                ->first();
        } catch (\Throwable $e) {
            // Tabla puede no existir o error de conexión
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Update check unavailable.',
            ]);
        }

        if (!$latest) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No active updates found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'version' => $latest->version,
                'release_notes' => $latest->release_notes,
                'download_url' => $latest->download_url ?? null,
                'force_update' => $latest->force_update ?? false,
                'published_at' => $latest->created_at?->toIso8601String(),
            ],
        ]);
    }
}
