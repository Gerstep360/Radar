<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Collection;

class MapService
{
    /**
     * Obtiene los puntos optimizados para el mapa
     * Solo trae los datos esenciales para renderizar marcadores
     */
    public function getMapPoints(?int $userId = null): Collection
    {
        $query = Report::query()
            ->select([
                'id',
                'title as titulo',
                'description as descripcion',
                'latitude',
                'longitude',
                'status as estado',
                'category_id',
                'created_at'
            ])
            ->with(['category:id,name,priority'])
            ->withCount('votes')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', 'rechazado')
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get();

        return $query->map(function ($report) use ($userId) {
            return [
                'id' => $report->id,
                'titulo' => $report->titulo,
                'descripcion' => $report->descripcion,
                'latitude' => $report->latitude,
                'longitude' => $report->longitude,
                'estado' => $report->estado,
                'votes_count' => $report->votes_count,
                'has_voted' => $userId ? $report->hasVotedBy($userId) : false,
                'category' => $report->category ? [
                    'id' => $report->category->id,
                    'name' => $report->category->name,
                    'priority' => $report->category->priority ?? 1
                ] : null,
                'created_at' => $report->created_at->toIso8601String()
            ];
        });
    }

    /**
     * Obtiene puntos filtrados por área geográfica (bounding box)
     */
    public function getPointsInBounds(float $minLat, float $maxLat, float $minLng, float $maxLng): Collection
    {
        return Report::query()
            ->select(['id', 'title as titulo', 'latitude', 'longitude', 'status as estado', 'category_id'])
            ->with(['category:id,name'])
            ->withCount('votes')
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', '!=', 'rechazado')
            ->limit(200)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'titulo' => $report->titulo,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'estado' => $report->estado,
                    'votes_count' => $report->votes_count,
                    'category' => $report->category?->name
                ];
            });
    }

    /**
     * Calcula clusters para marcadores solapados
     * Agrupa denuncias a menos de X metros de distancia
     */
    public function calculateClusters(
        int $zoom, 
        ?float $north = null, 
        ?float $south = null, 
        ?float $east = null, 
        ?float $west = null
    ): Collection {
        // Calcular threshold basado en zoom (más zoom = menos agrupación)
        $distanceThreshold = 0.001 * pow(2, (15 - $zoom));
        
        // Obtener puntos (filtrados por bounds si se proporcionan)
        if ($north && $south && $east && $west) {
            $points = $this->getPointsInBounds($south, $north, $west, $east);
        } else {
            $points = $this->getMapPoints();
        }

        $clusters = collect();
        $processed = collect();

        $points->each(function ($point) use (&$clusters, &$processed, $distanceThreshold) {
            if ($processed->contains($point['id'])) {
                return;
            }

            $cluster = [
                'center_lat' => $point['latitude'],
                'center_lng' => $point['longitude'],
                'items' => collect([$point]),
                'max_priority' => $point['category']['priority'] ?? 1
            ];

            $processed->push($point['id']);

            // Buscar puntos cercanos
            $points->each(function ($other) use (&$cluster, &$processed, $distanceThreshold, $point) {
                if ($processed->contains($other['id'])) {
                    return;
                }

                $distance = sqrt(
                    pow($point['latitude'] - $other['latitude'], 2) +
                    pow($point['longitude'] - $other['longitude'], 2)
                );

                if ($distance < $distanceThreshold) {
                    $cluster['items']->push($other);
                    $cluster['max_priority'] = max(
                        $cluster['max_priority'],
                        $other['category']['priority'] ?? 1
                    );
                    $processed->push($other['id']);
                }
            });

            $clusters->push([
                'lat' => $cluster['center_lat'],
                'lng' => $cluster['center_lng'],
                'count' => $cluster['items']->count(),
                'priority' => $cluster['max_priority'],
                'items' => $cluster['items']->take(5)->values() // Solo primeros 5 para preview
            ]);
        });

        return $clusters;
    }

    /**
     * Obtiene un punto individual por ID.
     * Útil para preview cards en el mapa.
     */
    public function getPoint(int $id): ?array
    {
        $report = Report::query()
            ->select([
                'id',
                'title as titulo',
                'description as descripcion',
                'latitude',
                'longitude',
                'status as estado',
                'category_id',
                'created_at'
            ])
            ->with(['category:id,name,priority'])
            ->withCount('votes')
            ->find($id);

        if (!$report) {
            return null;
        }

        return [
            'id' => $report->id,
            'titulo' => $report->titulo,
            'descripcion' => $report->descripcion,
            'latitude' => $report->latitude,
            'longitude' => $report->longitude,
            'estado' => $report->estado,
            'votes_count' => $report->votes_count,
            'category' => $report->category ? [
                'id' => $report->category->id,
                'name' => $report->category->name,
                'priority' => $report->category->priority ?? 1
            ] : null,
            'created_at' => $report->created_at->toIso8601String()
        ];
    }
}
