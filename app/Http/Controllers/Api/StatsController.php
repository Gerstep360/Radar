<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * API StatsController — Estadísticas optimizadas (REST para Tauri).
 */
class StatsController extends Controller
{
    /**
     * Dashboard con métricas generales (cacheado 10 min).
     */
    public function dashboard(): JsonResponse
    {
        $stats = Cache::remember('api_dashboard_stats', 600, function () {
            return [
                'totales' => [
                    'denuncias' => Report::count(),
                    'pendientes' => Report::where('status', 'pendiente')->count(),
                    'en_revision' => Report::where('status', 'en_revision')->count(),
                    'atendidas' => Report::where('status', 'atendido')->count(),
                    'desestimadas' => Report::where('status', 'desestimado')->count(),
                    'usuarios' => User::count(),
                    'votos_totales' => DB::table('votes')->count(),
                    'comentarios_totales' => DB::table('comments')->count(),
                ],
                'por_estado' => Report::query()
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray(),
                'por_categoria' => Category::query()
                    ->withCount('reports')
                    ->orderByDesc('reports_count')
                    ->get(['id', 'name', 'reports_count'])
                    ->toArray(),
                'ultimos_7_dias' => $this->getLast7Days(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'cached_until' => now()->addMinutes(10)->toIso8601String(),
        ]);
    }

    /**
     * Top 10 usuarios más activos.
     */
    public function topUsuarios(): JsonResponse
    {
        $usuarios = Cache::remember('api_top_users', 600, function () {
            return User::query()
                ->withCount('reports')
                ->having('reports_count', '>', 0)
                ->orderByDesc('reports_count')
                ->limit(10)
                ->get(['id', 'name', 'reports_count'])
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $usuarios,
        ]);
    }

    /**
     * Denuncias trending (más votadas no atendidas).
     */
    public function trending(): JsonResponse
    {
        $trending = Cache::remember('api_trending', 300, function () {
            return Report::query()
                ->withCount('votes')
                ->with(['category:id,name'])
                ->where('status', '!=', 'atendido')
                ->orderByDesc('votes_count')
                ->limit(10)
                ->get(['id', 'title', 'status', 'category_id', 'created_at'])
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $trending,
        ]);
    }

    /**
     * Invalidar toda la caché de estadísticas.
     */
    public function invalidateCache(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isAdmin() || $user->isModerator()), 403, 'No autorizado.');

        Cache::forget('api_dashboard_stats');
        Cache::forget('api_top_users');
        Cache::forget('api_trending');

        return response()->json([
            'success' => true,
            'message' => 'Caché de estadísticas invalidado.',
        ]);
    }

    private function getLast7Days(): array
    {
        $datos = Report::query()
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->pluck('total', 'fecha')
            ->toArray();

        $resultado = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $resultado[$fecha] = $datos[$fecha] ?? 0;
        }

        return $resultado;
    }
}
