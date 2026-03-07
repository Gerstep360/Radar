<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class VoteUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public int $reportId;
    public int $votesCount;
    public bool $voted;
    public ?int $userId;

    /**
     * @param Report $report
     * @param bool   $voted     true = votó, false = quitó voto
     * @param int|null $userId  usuario que hizo la acción
     * @param int|null $freshCount  conteo fresco (evita re-contar en el evento)
     */
    public function __construct(Report $report, bool $voted, ?int $userId = null, ?int $freshCount = null)
    {
        $this->reportId = $report->id;
        $this->votesCount = $freshCount ?? $report->votes()->count();
        $this->voted = $voted;
        $this->userId = $userId;
    }

    /**
     * Canal público - todos ven los contadores actualizados
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('radar'),
            new Channel('report.' . $this->reportId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'vote.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->reportId,
            'votes_count' => $this->votesCount,
            'voted' => $this->voted,
            'user_id' => $this->userId,
        ];
    }
}
