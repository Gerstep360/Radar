<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ReportCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    // Pre-calculamos todos los datos para evitar queries al serializar
    public int $reportId;
    public string $title;
    public string $description;
    public float $latitude;
    public float $longitude;
    public string $status;
    public ?array $category;
    public ?array $user;
    public array $media;
    public string $createdAt;

    public function __construct(Report $report)
    {
        $report->load('category', 'user', 'media');

        $this->reportId = $report->id;
        $this->title = $report->title;
        $this->description = $report->description;
        $this->latitude = $report->latitude;
        $this->longitude = $report->longitude;
        $this->status = $report->status;
        $this->category = $report->category ? [
            'id' => $report->category->id,
            'name' => $report->category->name,
        ] : null;
        $this->user = $report->user ? [
            'id' => $report->user->id,
            'name' => $report->user->name,
        ] : null;
        $this->media = $report->media->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->url,
        ])->toArray();
        $this->createdAt = $report->created_at?->toIso8601String() ?? now()->toIso8601String();
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
            'id' => $this->reportId,
            'title' => $this->title,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'category' => $this->category,
            'user' => $this->user,
            'votes_count' => 0,
            'comments_count' => 0,
            'media' => $this->media,
            'created_at' => $this->createdAt,
        ];
    }
}
