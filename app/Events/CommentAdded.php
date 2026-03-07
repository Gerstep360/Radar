<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class CommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    // Datos pre-calculados para el broadcast (evita queries extra)
    public int $commentId;
    public int $reportId;
    public string $content;
    public bool $isOfficial;
    public int $userId;
    public string $userName;
    public string $createdAt;
    public string $createdAtIso;

    public function __construct(Comment $comment)
    {
        // Pre-calcular todo aquí para que broadcastWith sea instantáneo
        // y no necesite re-cargar relaciones
        $this->commentId = $comment->id;
        $this->reportId  = $comment->report_id;
        $this->content   = $comment->content;
        $this->isOfficial = (bool) $comment->is_official;
        $this->userId    = $comment->user?->id ?? $comment->user_id;
        $this->userName  = $comment->user?->name ?? 'Usuario';
        $this->createdAt = $comment->created_at?->diffForHumans() ?? 'Ahora';
        $this->createdAtIso = $comment->created_at?->toIso8601String() ?? now()->toIso8601String();
    }

    /**
     * Canal específico del reporte + canal global para contadores
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('report.' . $this->reportId),
            new Channel('radar'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.added';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->commentId,
            'report_id' => $this->reportId,
            'content' => $this->content,
            'is_official' => $this->isOfficial,
            'user' => [
                'id' => $this->userId,
                'name' => $this->userName,
                'initial' => substr($this->userName, 0, 1),
            ],
            'created_at' => $this->createdAt,
            'created_at_iso' => $this->createdAtIso,
        ];
    }
}
