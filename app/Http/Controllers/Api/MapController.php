<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * API MapController — Endpoints del mapa optimizados (REST para Tauri).
 */
class MapController extends Controller
{
    public function __construct(
        private MapService $mapService
    ) {}

    /**
     * Puntos del mapa (carga inicial).
     * Personalizado por usuario (has_voted).
     */
    public function points(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $points = $this->mapService->getMapPoints($userId);

        return response()->json([
            'success' => true,
            'data' => $points,
            'count' => $points->count(),
        ]);
    }

    /**
     * Punto individual por ID.
     */
    public function point(Request $request, int $id): JsonResponse
    {
        $point = $this->mapService->getPoint($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Punto no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $point,
        ]);
    }

    /**
     * Puntos dentro de un bounding box (lazy loading del mapa).
     */
    public function pointsInBounds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'north' => 'required|numeric|between:-90,90',
            'south' => 'required|numeric|between:-90,90',
            'east' => 'required|numeric|between:-180,180',
            'west' => 'required|numeric|between:-180,180',
        ]);

        $points = $this->mapService->getPointsInBounds(
            $validated['south'],
            $validated['north'],
            $validated['west'],
            $validated['east']
        );

        return response()->json([
            'success' => true,
            'data' => $points,
            'count' => $points->count(),
            'bounds' => $validated,
        ]);
    }

    /**
     * Clusters de marcadores agrupados.
     */
    public function clusters(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'zoom' => 'required|integer|min:1|max:20',
            'north' => 'nullable|numeric|between:-90,90',
            'south' => 'nullable|numeric|between:-90,90',
            'east' => 'nullable|numeric|between:-180,180',
            'west' => 'nullable|numeric|between:-180,180',
        ]);

        $clusters = $this->mapService->calculateClusters(
            $validated['zoom'],
            $validated['north'] ?? null,
            $validated['south'] ?? null,
            $validated['east'] ?? null,
            $validated['west'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $clusters,
            'zoom' => $validated['zoom'],
        ]);
    }

    /**
     * Config para WebSocket (Reverb).
     */
    public function realtimeConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'websocket' => [
                'enabled' => config('broadcasting.default') === 'reverb',
                'channel' => 'map.updates',
                'events' => [
                    'report.created' => 'App\\Events\\ReportCreated',
                    'report.status_changed' => 'App\\Events\\ReportStatusChanged',
                    'vote.updated' => 'App\\Events\\VoteUpdated',
                    'comment.added' => 'App\\Events\\CommentAdded',
                ],
            ],
        ]);
    }

    /**
     * Invalidar caché del mapa.
     */
    public function invalidateCache(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isModerator()), 403, 'No autorizado.');

        Cache::forget('map_points_guest');

        return response()->json([
            'success' => true,
            'message' => 'Caché del mapa invalidado.',
        ]);
    }
}
