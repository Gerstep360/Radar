<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\Report;
use App\Events\VoteUpdated;
use Illuminate\Support\Facades\DB;

class VoteService
{
    /**
     * Votar o quitar voto de un reporte
     */
    public function toggleVote(Report $report, int $userId): array
    {
        return DB::transaction(function () use ($report, $userId) {
            // Verificar si ya votó
            $existingVote = Vote::where('report_id', $report->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingVote) {
                // Quitar voto
                $existingVote->delete();
                
                $result = [
                    'action' => 'removed',
                    'voted' => false,
                    'votes_count' => $report->votes()->count(),
                    'message' => 'Voto removido'
                ];
                
                // 🔴 Broadcast en tiempo real
                broadcast(new VoteUpdated($report, false, $userId))->toOthers();
                
                return $result;
            }

            // Agregar voto
            Vote::create([
                'user_id' => $userId,
                'report_id' => $report->id
            ]);

            $result = [
                'action' => 'added',
                'voted' => true,
                'votes_count' => $report->votes()->count(),
                'message' => 'Voto agregado'
            ];
            
            // 🔴 Broadcast en tiempo real
            broadcast(new VoteUpdated($report, true, $userId))->toOthers();

            return $result;
        });
    }

    /**
     * Obtener reportes con más votos (prioridad comunitaria)
     */
    public function getTopVotedReports(int $limit = 10)
    {
        return Report::withCount('votes')
            ->where('status', '!=', 'atendido')
            ->orderBy('votes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calcular prioridad efectiva (categoría + votos comunitarios)
     */
    public function calculateEffectivePriority(Report $report): int
    {
        $categoryPriority = $report->category->priority ?? 1; // 1-5
        $voteBoost = min(floor($report->votes_count / 5), 2); // Cada 5 votos = +1 prioridad (máx +2)
        
        return min($categoryPriority + $voteBoost, 5); // Nunca superar 5
    }
}
