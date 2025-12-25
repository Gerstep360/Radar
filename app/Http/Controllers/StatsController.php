<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * StatsController - Estadísticas de la plataforma.
 * 
 * Responsabilidades:
 * - Métricas generales para dashboard
 * - Conteos por categoría, estado, fecha
 * - Usuarios más activos
 */
class StatsController extends Controller
{
    /**
     * Estadísticas generales del dashboard.
     * Cacheadas por 10 minutos para optimizar.
     */
    public function dashboard(): JsonResponse
    {
        $stats = Cache::remember('dashboard_stats', 600, function () {
            return [
                'totales' => $this->getTotales(),
                'por_estado' => $this->getPorEstado(),
                'por_categoria' => $this->getPorCategoria(),
                'ultimos_7_dias' => $this->getUltimos7Dias(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'cached_until' => now()->addMinutes(10)->toIso8601String()
        ]);
    }

    /**
     * Contadores totales.
     */
    private function getTotales(): array
    {
        return [
            'denuncias' => Report::count(),
            'pendientes' => Report::where('status', 'pendiente')->count(),
            'en_proceso' => Report::where('status', 'en_proceso')->count(),
            'atendidas' => Report::where('status', 'atendido')->count(),
            'usuarios' => User::count(),
            'votos_totales' => DB::table('votes')->count(),
        ];
    }

    /**
     * Denuncias agrupadas por estado.
     */
    private function getPorEstado(): array
    {
        return Report::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Denuncias agrupadas por categoría.
     */
    private function getPorCategoria(): array
    {
        return Category::query()
            ->withCount('reports')
            ->orderByDesc('reports_count')
            ->get(['id', 'name', 'reports_count'])
            ->toArray();
    }

    /**
     * Denuncias de los últimos 7 días (para gráfico).
     */
    private function getUltimos7Dias(): array
    {
        $datos = Report::query()
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        // Rellenar días sin datos con 0
        $resultado = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $resultado[$fecha] = $datos[$fecha] ?? 0;
        }

        return $resultado;
    }

    /**
     * Usuarios más activos (top 10).
     */
    public function topUsuarios(): JsonResponse
    {
        $usuarios = User::query()
            ->withCount('reports')
            ->having('reports_count', '>', 0)
            ->orderByDesc('reports_count')
            ->limit(10)
            ->get(['id', 'name', 'reports_count']);

        return response()->json([
            'success' => true,
            'data' => $usuarios
        ]);
    }

    /**
     * Denuncias con más votos (trending).
     */
    public function trending(): JsonResponse
    {
        $trending = Report::query()
            ->withCount('votes')
            ->with(['category:id,name'])
            ->where('status', '!=', 'atendido')
            ->orderByDesc('votes_count')
            ->limit(10)
            ->get(['id', 'title', 'status', 'category_id', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $trending
        ]);
    }

    /**
     * Invalidar caché de estadísticas.
     * Llamar cuando se crea/actualiza una denuncia.
     */
    public function invalidateCache(): JsonResponse
    {
        Cache::forget('dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Caché de estadísticas invalidado'
        ]);
    }
}
