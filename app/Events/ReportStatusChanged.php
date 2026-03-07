<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ReportStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public int $reportId;
    public string $title;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Report $report, string $oldStatus)
    {
        $this->reportId = $report->id;
        $this->title = $report->title;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $report->status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('radar'),
            new Channel('report.' . $this->reportId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'report.status-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reportId,
            'title' => $this->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
