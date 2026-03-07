<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseChannel
{
    /**
     * @var Messaging
     */
    protected $messaging;

    /**
     * Create a new Firebase channel instance.
     *
     * @param Messaging $messaging
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        $message = $notification->toFirebase($notifiable);

        if (!$message) {
            return;
        }

        // Obtener los tokens del usuario
        $tokens = $notifiable->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        foreach ($tokens as $token) {
            try {
                // El token ya viene desencriptado por el 'encrypted' cast del modelo FcmToken
                $this->messaging->send(
                    CloudMessage::fromArray(array_merge($message, ['token' => $token]))
                );
            } catch (\Exception $e) {
                // Podríamos limpiar tokens inválidos aquí en el futuro
                \Log::error("Error enviando notificación Firebase a token: " . $e->getMessage());
            }
        }
    }
}
