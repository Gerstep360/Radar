<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Report;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Overview extends Component
{
    public function render()
    {
        $baseQuery = Report::query();

        // KPI PRINCIPALES
        $totalReports      = (clone $baseQuery)->count();
        $pendingReports    = (clone $baseQuery)->whereIn('status', ['pendiente', 'pending'])->count();
        $inProgressReports = (clone $baseQuery)->whereIn('status', ['en_revision', 'in_progress', 'en_proceso'])->count();
        $resolvedReports   = (clone $baseQuery)->whereIn('status', ['atendido', 'resolved', 'resuelto'])->count();
        $rejectedReports   = (clone $baseQuery)->whereIn('status', ['desestimado', 'rejected', 'rechazado'])->count();
        $resolutionRate    = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100, 1) : 0;

        $resolvedReportsCollection = (clone $baseQuery)->whereIn('status', ['atendido', 'resolved', 'resuelto'])->whereNotNull('updated_at')->get();
        $totalHours = 0;
        foreach($resolvedReportsCollection as $rep) {
            $totalHours += $rep->created_at->diffInHours($rep->updated_at);
        }
        $averageResolutionTime = $resolvedReportsCollection->count() > 0 ? round($totalHours / $resolvedReportsCollection->count(), 1) : 0;

        $criticalReportsCount = (clone $baseQuery)->has('votes', '>=', 5)
            ->whereIn('status', ['pendiente', 'pending', 'en_revision', 'in_progress', 'en_proceso'])
            ->count();

        // LISTAS DE ATENCIÓN
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

        // GRÁFICAS Y DISTRIBUCIONES
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

        $statusDistribution = (clone $baseQuery)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $dailyQuery = clone $baseQuery;
        $dailyReports = $dailyQuery->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dateArray = collect();
        for ($i = 13; $i >= 0; $i--) {
            $dateFormatedDb = now()->subDays($i)->format('Y-m-d');
            $dateArray->push([
                'date'  => now()->subDays($i)->format('d/m'),
                'count' => $dailyReports->get($dateFormatedDb)?->count ?? 0,
            ]);
        }
        $last14Days = $dateArray;

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


        // Variables requeridas por Blade para la carga de Chart.js
        $statusChartLabels = $statusDistribution->pluck('status')->map(fn($s) => ucfirst($s))->toJson();
        $statusChartData = $statusDistribution->pluck('count')->toJson();

        $categoryChartLabels = $categoryDistribution->pluck('name')->toJson();
        $categoryChartData = $categoryDistribution->pluck('reports_count')->toJson();

        $evolutionChartLabels = collect($last14Days)->pluck('date')->toJson();
        $evolutionChartData = collect($last14Days)->pluck('count')->toJson();

        return view('livewire.admin.overview', compact(
            'totalReports',
            'pendingReports',
            'inProgressReports',
            'resolvedReports',
            'rejectedReports',
            'resolutionRate',
            'averageResolutionTime',
            'criticalReportsCount',
            'topVoted',
            'urgentReports',
            'categoryDistribution',
            'statusDistribution',
            'last14Days',
            'topUsers',
            'statusChartLabels',
            'statusChartData',
            'categoryChartLabels',
            'categoryChartData',
            'evolutionChartLabels',
            'evolutionChartData'
        ));
    }

    public function updateReportStatus($reportId, $newStatus)
    {
        $report = Report::find($reportId);
        if ($report && in_array($newStatus, ['pendiente','en_revision','atendido','desestimado','pending','in_progress','resolved','rejected','en_proceso','resuelto','rechazado'])) {
            $report->status = $newStatus;
            $report->save();
            session()->flash('success', 'Estado actualizado correctamente.');
        }
    }
}
