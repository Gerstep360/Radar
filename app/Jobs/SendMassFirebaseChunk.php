<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\SendFirebaseAlert;
use App\Models\User;

class SendMassFirebaseChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param int[] $userIds */
    public function __construct(
        public readonly array $userIds,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $icon = null,
        public readonly ?string $actionUrl = null,
        public readonly ?string $image = null,
        public readonly array $data = [],
    ) {}

    /**
     * Procesar el chunk para Firebase.
     */
    public function handle(): void
    {
        $notification = new SendFirebaseAlert(
            title:     $this->title,
            body:      $this->body,
            icon:      $this->icon,
            actionUrl: $this->actionUrl,
            image:     $this->image,
            data:      $this->data,
        );

        User::whereIn('id', $this->userIds)
            ->whereHas('fcmTokens')
            ->lazyById(50)
            ->each(function (User $user) use ($notification) {
                try {
                    $user->notify(clone $notification);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning(
                        "[FirebasePush] No se pudo notificar al usuario {$user->id}: " . $e->getMessage()
                    );
                }
            });
    }
}
