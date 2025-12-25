<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load('user');
    }

    /**
     * Canal específico del reporte
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('report.' . $this->comment->report_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.added';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->comment->id,
            'report_id' => $this->comment->report_id,
            'content' => $this->comment->content,
            'is_official' => $this->comment->is_official,
            'user' => [
                'id' => $this->comment->user?->id,
                'name' => $this->comment->user?->name,
                'initial' => substr($this->comment->user?->name ?? 'U', 0, 1),
            ],
            'created_at' => $this->comment->created_at->diffForHumans(),
        ];
    }
}
