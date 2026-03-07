<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\Report;
use App\Events\VoteUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VoteService
{
    /**
     * Votar o quitar voto de un reporte.
     * Usa lock atómico por usuario+reporte para evitar race conditions
     * en clics rápidos (dar like / quitar like repetidamente).
     */
    public function toggleVote(Report $report, int $userId): array
    {
        // Lock atómico: impide que el mismo usuario procese 2 toggles simultáneos
        $lockKey = "vote_lock:{$userId}:{$report->id}";
        $lock = Cache::lock($lockKey, 5); // 5 segundos máximo

        if (!$lock->get()) {
            // Si no puede adquirir el lock, devolver estado actual sin modificar
            $currentCount = $report->votes()->count();
            $hasVoted = Vote::where('report_id', $report->id)
                ->where('user_id', $userId)
                ->exists();

            return [
                'action' => 'throttled',
                'voted' => $hasVoted,
                'votes_count' => $currentCount,
                'message' => 'Procesando voto anterior, intenta de nuevo',
            ];
        }

        try {
            return DB::transaction(function () use ($report, $userId) {
                // lockForUpdate previene lecturas sucias dentro de la transacción
                $existingVote = Vote::where('report_id', $report->id)
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if ($existingVote) {
                    $existingVote->delete();

                    // Contar DESPUÉS del delete para datos frescos
                    $freshCount = Vote::where('report_id', $report->id)->count();

                    $result = [
                        'action' => 'removed',
                        'voted' => false,
                        'votes_count' => $freshCount,
                        'message' => 'Voto removido',
                    ];

                    broadcast(new VoteUpdated($report, false, $userId, $freshCount))->toOthers();

                    return $result;
                }

                // Crear voto (unique constraint en DB como respaldo)
                try {
                    Vote::create([
                        'user_id' => $userId,
                        'report_id' => $report->id,
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Si por alguna razón ya existía (edge case extremo), retornar estado actual
                    $freshCount = Vote::where('report_id', $report->id)->count();
                    return [
                        'action' => 'already_voted',
                        'voted' => true,
                        'votes_count' => $freshCount,
                        'message' => 'Ya habías votado',
                    ];
                }

                $freshCount = Vote::where('report_id', $report->id)->count();

                $result = [
                    'action' => 'added',
                    'voted' => true,
                    'votes_count' => $freshCount,
                    'message' => 'Voto agregado',
                ];

                broadcast(new VoteUpdated($report, true, $userId, $freshCount))->toOthers();

                $this->checkAndSendMilestoneNotification($report, $freshCount, $userId);

                return $result;
            });
        } finally {
            $lock->release();
        }
    }

    /**
     * Verifica si se alcanzó un hito de likes y envía la notificación.
     *
     * Hitos exactos para no saturar el backend con notificaciones por miserias:
     *   - Hasta 1000: 10, 50, 100, 150, 200, 300, 500, 700, 1000
     *   - Pasados 1000: cada 1000 (2000, 3000, ..., 7000, ...)
     */
    protected function checkAndSendMilestoneNotification(Report $report, int $votesCount, int $voterId): void
    {
        // No notificar si el usuario se vota a su propio reporte
        if ($report->user_id === $voterId) {
            return;
        }

        $milestones = [10, 50, 100, 150, 200, 300, 500, 700, 1000];
        $isMilestone = false;

        if ($votesCount <= 1000) {
            $isMilestone = in_array($votesCount, $milestones);
        } else {
            // Pasados los 1000, solo notificar en múltiplos exactos de 1000
            $isMilestone = ($votesCount % 1000 === 0);
        }

        if (!$isMilestone) {
            return;
        }

        // 1️⃣ Notificación en base de datos (canal Reverb para el frontend en tiempo real)
        $notification = \App\Models\Notification::create([
            'user_id'   => $report->user_id,
            'report_id' => $report->id,
            'type'      => 'vote',
            'title'     => '¡Tu denuncia está ganando tracción!',
            'message'   => "Tu reporte ha alcanzado los {$votesCount} apoyos.",
            'data'      => [
                'votes_count'  => $votesCount,
                'report_title' => $report->title,
            ],
        ]);

        event(new \App\Events\NotificationCreated($notification));

        // 2️⃣ Firebase Push nativo al dueño del reporte
        $owner = \App\Models\User::find($report->user_id);
        if ($owner && $owner->fcmTokens()->exists()) {
            $owner->notify(new \App\Notifications\SendFirebaseAlert(
                title:     '🎉 ¡' . $votesCount . ' apoyos en tu reporte!',
                body:      "Tu denuncia \"{$report->title}\" ha llegado a {$votesCount} votos. La comunidad te respalda.",
                icon:      '/icon.png',
                actionUrl: '/denuncias/' . $report->id,
            ));
        }
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
