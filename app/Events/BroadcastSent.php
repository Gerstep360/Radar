<?php

namespace App\Events;

use App\Models\Broadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastSent implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcast;

    /**
     * El sistema de colas (Queues) permitirá que el VPS no se sature
     * al procesar el envío de forma asíncrona.
     */
    public $queue = 'broadcasts';

    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Canal público 'radar' — todos los dispositivos escuchan.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('radar'),
        ];
    }

    /**
     * Nombre del evento que recibirá el frontend.
     */
    public function broadcastAs(): string
    {
        return 'BroadcastSent';
    }

    /**
     * Payload completo que llega al cliente.
     */
    public function broadcastWith(): array
    {
        return [
            'id'                 => $this->broadcast->id,
            'type'               => $this->broadcast->type,
            'title'              => $this->broadcast->title,
            'body'               => $this->broadcast->body,
            'image_url'          => $this->broadcast->image_url,
            'is_popup'           => $this->broadcast->is_popup,
            'auto_close_seconds' => $this->broadcast->auto_close_seconds,
            'action_url'         => $this->broadcast->action_url,
            'icon'               => $this->broadcast->effective_icon,
            'color'              => $this->broadcast->effective_color,
            'expires_at'         => $this->broadcast->expires_at?->toIso8601String(),
            'sent_by'            => $this->broadcast->sent_by,
            'sent_at'            => $this->broadcast->created_at->toIso8601String(),
        ];
    }
}
