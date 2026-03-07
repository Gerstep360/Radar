<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $baseQuery = Report::query();

        // 1. Aplicar Filtros Globales (Buscador, Categoría, Estado, Fechas)
        if ($request->search) {
            $search = $request->search;
            $baseQuery->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->category_id) {
            $baseQuery->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $baseQuery->where('status', $request->status);
        }
        if ($request->date_from) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // ==========================================
        //  PAGINACIÓN PRINCIPAL PARA LA TABLA
        // ==========================================
        $query = clone $baseQuery;
        
        $query->with(['user', 'category', 'media', 'votes']);
        if ($request->priority === 'alta') {
            $query->withCount('votes')->orderByDesc('votes_count');
        } else {
            $query->latest();
        }
        $reports = $query->paginate(20)->withQueryString();

        // ==========================================
        //  KPI PRINCIPALES (Globales)
        // ==========================================
        $totalReports      = (clone $baseQuery)->count();
        $pendingReports    = (clone $baseQuery)->whereIn('status', ['pendiente', 'pending'])->count();
        $inProgressReports = (clone $baseQuery)->whereIn('status', ['en_revision', 'in_progress', 'en_proceso'])->count();
        $resolvedReports   = (clone $baseQuery)->whereIn('status', ['atendido', 'resolved', 'resuelto'])->count();
        $rejectedReports   = (clone $baseQuery)->whereIn('status', ['desestimado', 'rejected', 'rechazado'])->count();
        $resolutionRate    = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100, 1) : 0;

        // Tiempo Promedio Acción
        $resolvedReportsCollection = (clone $baseQuery)->whereIn('status', ['atendido', 'resolved', 'resuelto'])->whereNotNull('updated_at')->get();
        $totalHours = 0;
        foreach($resolvedReportsCollection as $rep) {
            $totalHours += $rep->created_at->diffInHours($rep->updated_at);
        }
        $averageResolutionTime = $resolvedReportsCollection->count() > 0 ? round($totalHours / $resolvedReportsCollection->count(), 1) : 0;

        // Problemas Críticos (Filtrados)
        $criticalReportsCount = (clone $baseQuery)->has('votes', '>=', 5)
            ->whereIn('status', ['pendiente', 'pending', 'en_revision', 'in_progress', 'en_proceso'])
            ->count();

        // ==========================================
        //  LISTAS DE ATENCIÓN (Filtradas)
        // ==========================================
        $topVoted = (clone $baseQuery)->with(['category', 'user'])
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->limit(5)
            ->get();

        $urgentReports = (clone $baseQuery)->with(['category', 'user', 'votes'])
            ->withCount('votes')
            ->whereIn('status', ['pendiente', 'pending', 'en_revision', 'in_progress'])
            ->orderByDesc('votes_count')
            ->orderBy('created_at')
            ->limit(8)
            ->get();

        // ==========================================
        //  GRÁFICAS Y DISTRIBUCIONES (Filtradas)
        // ==========================================
        
        // 1. Categorías
        // Necesitamos agrupar el $baseQuery por categoría. 
        // Obtenemos los IDs y el count, luego mapeamos los nombres.
        $catCounts = (clone $baseQuery)->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->pluck('count', 'category_id');
            
        $categories = Category::all();
        $categoryDistribution = $categories->map(function($cat) use ($catCounts) {
            return (object)[
                'name' => $cat->name,
                'reports_count' => $catCounts->get($cat->id, 0)
            ];
        })->filter(fn($c) => $c->reports_count > 0)->sortByDesc('reports_count')->values();

        // 2. Estado
        $statusDistribution = (clone $baseQuery)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 3. Evolución últimos n días (14 ó según el rango, pero forzaremos últimos 14 relativos al hoy para la gráfica de tendencia)
        $dailyQuery = clone $baseQuery;
        // Si no hay date_from/to en el request, mostramos siempre los últimos 14. 
        // Si hay, mostramos la gráfica basada en los días dentro de ese rango que tengan datos.
        $dailyReports = $dailyQuery->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Determinar periodo de la grafica
        $dateArray = collect();
        if ($request->date_from && $request->date_to) {
            $start = \Carbon\Carbon::parse($request->date_from);
            $end = \Carbon\Carbon::parse($request->date_to);
            if ($start->diffInDays($end) <= 31) {
                // Rellenar días del rango si es <= 1 mes
                for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                    $dstr = $d->format('Y-m-d');
                    $dateArray->push([
                        'date'  => $d->format('d/m'),
                        'count' => $dailyReports->get($dstr)?->count ?? 0,
                    ]);
                }
            } else {
                // Si es un rango enorme, solo graficar los días con info (agrupado)
                foreach($dailyReports as $date => $info) {
                    $dateArray->push(['date' => \Carbon\Carbon::parse($date)->format('d/m'), 'count' => $info->count]);
                }
            }
            $last14Days = $dateArray;
        } else {
            // Predeterminado últimos 14
            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dateArray->push([
                    'date'  => now()->subDays($i)->format('d/m'),
                    'count' => $dailyReports->get($date)?->count ?? 0,
                ]);
            }
            $last14Days = $dateArray;
        }

        // ==========================================
        //  PUNTOS DEL MAPA (Filtrados)
        // ==========================================
        $allPoints = (clone $baseQuery)->with('category')->get();

        // ==========================================
        //  USUARIOS ACTIVOS (Globales o Filtrados)
        // ==========================================
        $topUsersIds = (clone $baseQuery)->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'user_id');

        $topUsers = User::whereIn('id', $topUsersIds->keys())->get()
            ->map(function($user) use ($topUsersIds) {
                $user->reports_count = $topUsersIds[$user->id];
                return $user;
            })->sortByDesc('reports_count')->values();


        // --- Datos para Gráficas (Chart.js) ---
        $statusChartLabels = $statusDistribution->pluck('status')->map(fn($s) => ucfirst($s))->toJson();
        $statusChartData = $statusDistribution->pluck('count')->toJson();

        $categoryChartLabels = $categoryDistribution->pluck('name')->toJson();
        $categoryChartData = $categoryDistribution->pluck('reports_count')->toJson();

        $evolutionChartLabels = collect($last14Days)->pluck('date')->toJson();
        $evolutionChartData = collect($last14Days)->pluck('count')->toJson();

        return view('admin.dashboard', compact(
            'reports',
            'categories',
            'totalReports',
            'pendingReports',
            'inProgressReports',
            'resolvedReports',
            'rejectedReports',
            'resolutionRate',
            'topVoted',
            'urgentReports',
            'categoryDistribution',
            'statusDistribution',
            'last14Days',
            'allPoints',
            'topUsers',
            'averageResolutionTime',
            'criticalReportsCount',
            'statusChartLabels',
            'statusChartData',
            'categoryChartLabels',
            'categoryChartData',
            'evolutionChartLabels',
            'evolutionChartData'
        ));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|string|in:pendiente,en_revision,atendido,desestimado,pending,in_progress,resolved,rejected,en_proceso,resuelto,rechazado'
        ]);

        $report->status = $request->status;
        $report->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $report->status]);
        }

        return back()->with('success', 'Estado actualizado correctamente');
    }
}
