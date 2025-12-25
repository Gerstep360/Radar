<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $reportId;
    public int $votesCount;
    public bool $voted;
    public ?int $userId;

    public function __construct(Report $report, bool $voted, ?int $userId = null)
    {
        $this->reportId = $report->id;
        $this->votesCount = $report->votes()->count();
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
