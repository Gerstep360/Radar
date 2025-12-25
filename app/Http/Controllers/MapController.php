<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

/**
 * MapController - Controlador dedicado para operaciones del mapa.
 * 
 * Responsabilidades:
 * - Endpoints de puntos para el mapa (JSON)
 * - Clustering de marcadores
 * - Puntos en área geográfica (bounding box)
 * - Preparado para WebSockets con Laravel Reverb
 * 
 * NO maneja: CRUD de denuncias (DenunciaController), votos (VoteController)
 */
class MapController extends Controller
{
    public function __construct(
        private MapService $mapService
    ) {}

    /**
     * Obtener todos los puntos del mapa.
     * 
     * Usado para carga inicial del mapa con denuncias.
     * Incluye has_voted para el usuario autenticado.
     */
    public function points(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        
        // Sin caché para datos personalizados (has_voted depende del usuario)
        $points = $this->mapService->getMapPoints($userId);

        return response()->json([
            'success' => true,
            'data' => $points,
            'count' => count($points)
        ]);
    }

    /**
     * Obtener puntos dentro de un área específica (bounding box).
     * 
     * Útil para carga lazy cuando el usuario mueve el mapa.
     * Más eficiente que cargar todos los puntos de una vez.
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
            $validated['north'],
            $validated['south'],
            $validated['east'],
            $validated['west']
        );

        return response()->json([
            'success' => true,
            'data' => $points,
            'count' => count($points),
            'bounds' => $validated
        ]);
    }

    /**
     * Obtener clusters de puntos agrupados.
     * 
     * Agrupa puntos cercanos para evitar sobrecarga visual
     * cuando hay muchas denuncias en la misma zona.
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
            'zoom' => $validated['zoom']
        ]);
    }

    /**
     * Obtener punto individual por ID.
     * 
     * Usado para preview cards en el mapa.
     */
    public function point(int $id): JsonResponse
    {
        $point = $this->mapService->getPoint($id);

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Punto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $point
        ]);
    }

    /**
     * Invalidar caché del mapa.
     * 
     * Llamar después de crear/actualizar/eliminar denuncias.
     * También se puede llamar desde eventos de Reverb.
     */
    public function invalidateCache(): JsonResponse
    {
        Cache::forget('map_points_guest');
        // Podrías invalidar más claves específicas aquí
        
        return response()->json([
            'success' => true,
            'message' => 'Caché invalidado'
        ]);
    }

    /**
     * Endpoint para WebSocket heartbeat.
     * 
     * Preparado para cuando se integre Laravel Reverb.
     * Los clientes pueden suscribirse a actualizaciones en tiempo real.
     */
    public function realtimeConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'websocket' => [
                'enabled' => config('broadcasting.default') === 'reverb',
                'channel' => 'map.updates',
                'events' => [
                    'denuncia.created' => 'App\\Events\\DenunciaCreated',
                    'denuncia.updated' => 'App\\Events\\DenunciaUpdated',
                    'denuncia.deleted' => 'App\\Events\\DenunciaDeleted',
                ]
            ]
        ]);
    }
}
