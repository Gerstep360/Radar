<?php

namespace App\Notifications;

use App\Channels\FirebaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendFirebaseAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $body;
    public $icon;
    public $actionUrl;
    public $image;
    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body, $icon = null, $actionUrl = null, $image = null, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
        $this->actionUrl = $actionUrl;
        $this->image = $image;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {
        $payload = [
            'notification' => [
                'title' => $this->title,
                'body' => $this->body,
            ],
            'data' => array_merge([
                'url' => $this->actionUrl ?: '/',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // O el equivalente en Tauri si aplica
            ], $this->data)
        ];

        if ($this->image) {
            $payload['notification']['image'] = $this->image;
        }

        // Si tenemos un ícono específico, lo pasamos en data o notification según plataforma
        if ($this->icon) {
            $payload['data']['icon'] = $this->icon;
        }

        return $payload;
    }
}
