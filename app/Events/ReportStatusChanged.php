<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Report $report;
    public string $oldStatus;

    public function __construct(Report $report, string $oldStatus)
    {
        $this->report = $report;
        $this->oldStatus = $oldStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('radar'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'report.status-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->report->id,
            'title' => $this->report->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->report->status,
        ];
    }
}
