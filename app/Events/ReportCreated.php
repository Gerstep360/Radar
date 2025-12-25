<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report->load('category', 'user');
    }

    /**
     * Canal público para que todos vean nuevos reportes
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('radar'),
        ];
    }

    /**
     * Nombre del evento en el frontend
     */
    public function broadcastAs(): string
    {
        return 'report.created';
    }

    /**
     * Datos que se envían al frontend
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->report->id,
            'title' => $this->report->title,
            'description' => $this->report->description,
            'latitude' => $this->report->latitude,
            'longitude' => $this->report->longitude,
            'status' => $this->report->status,
            'category' => [
                'id' => $this->report->category?->id,
                'name' => $this->report->category?->name,
            ],
            'user' => [
                'id' => $this->report->user?->id,
                'name' => $this->report->user?->name,
            ],
            'votes_count' => 0,
            'created_at' => $this->report->created_at->toISOString(),
        ];
    }
}
