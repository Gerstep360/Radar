<?php

namespace App\Events;

use App\Models\AppUpdate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppUpdatePublished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $update;

    /**
     * Create a new event instance.
     */
    public function __construct(AppUpdate $update)
    {
        $this->update = $update;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Se envía por el canal público 'radar'
        return [
            new Channel('radar'),
        ];
    }

    /**
     * El nombre del evento que se emitirá por el websocket.
     */
    public function broadcastAs(): string
    {
        return 'AppUpdatePublished';
    }

    /**
     * Datos que se enviarán con el evento.
     */
    public function broadcastWith(): array
    {
        return [
            'version' => $this->update->version,
            'release_notes' => $this->update->release_notes,
            'download_url' => $this->update->download_url, // Getter attribute
            'force_update' => $this->update->force_update,
            'published_at' => $this->update->created_at->toIso8601String(),
        ];
    }
}
