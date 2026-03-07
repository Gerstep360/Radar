<?php

namespace App\Console\Commands;

use App\Events\BroadcastSent;
use App\Jobs\SendMassFirebaseChunk;
use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Console\Command;

class BroadcastSendCommand extends Command
{
    protected $signature = 'broadcast:send
                            {title : Título de la notificación}
                            {body? : Cuerpo del mensaje (opcional si usas --markdown)}
                            {--type=notification : Tipo: notification, announcement, alert, update, maintenance, custom}
                            {--action-url= : URL que la app procesará al hacer click (relativa: /api/app/download)}
                            {--image= : URL de imagen para mostrar en la notificación o popup}
                            {--popup : Si se debe mostrar como ventana flotante (burbuja)}
                            {--auto-close=10 : Segundos antes de que el popup se cierre solo}
                            {--markdown= : Nombre del archivo en storage/app/updates/ (ej: v2.md)}
                            {--icon= : Nombre del ícono (bell, megaphone, triangle, download, wrench, star)}
                            {--color= : Color hex del banner (ej: #FF5733)}
                            {--expires= : Expirar en N horas a partir de ahora (ej: 2 = 2 horas)}
                            {--sent-by=Admin : Nombre del remitente}
                            {--no-push : Omitir el envío de notificaciones Web Push nativas}
                            {--chunk=300 : Tamaño de cada lote de Web Push (default 300 por chunk)}';

    protected $description = 'Envía una notificación/anuncio/alerta a todos los dispositivos con la app en tiempo real.';

    const VALID_TYPES = ['notification', 'announcement', 'alert', 'update', 'maintenance', 'custom'];

    public function handle()
    {
        $type = $this->option('type');
        $body = $this->argument('body');
        $markdownFile = $this->option('markdown');

        if (!in_array($type, self::VALID_TYPES)) {
            $this->error("Tipo inválido: '{$type}'");
            $this->line("Tipos disponibles: " . implode(', ', self::VALID_TYPES));
            return Command::FAILURE;
        }

        // Lógica de Markdown
        if ($markdownFile) {
            $path = storage_path("app/updates/{$markdownFile}");
            if (!file_exists($path)) {
                $this->error("Archivo markdown no encontrado: {$path}");
                return Command::FAILURE;
            }
            $body = file_get_contents($path);
            $this->info("📖 Notas cargadas desde markdown: {$markdownFile}");
        }

        if (!$body) {
            $this->error("Debes proporcionar un 'body' o usar '--markdown=archivo.md'");
            return Command::FAILURE;
        }

        $expiresAt = null;
        if ($this->option('expires')) {
            $hours = (float) $this->option('expires');
            if ($hours <= 0) {
                $this->error("El valor de --expires debe ser mayor a 0 (horas).");
                return Command::FAILURE;
            }
            $expiresAt = now()->addHours($hours);
        }

        // Crear el registro en la base de datos
        $broadcast = Broadcast::create([
            'type'               => $type,
            'title'              => $this->argument('title'),
            'body'               => $body,
            'action_url'         => $this->option('action-url'),
            'image_url'          => $this->option('image'),
            'is_popup'           => $this->option('popup'),
            'auto_close_seconds' => (int) $this->option('auto-close'),
            'icon'               => $this->option('icon'),
            'color'              => $this->option('color'),
            'expires_at'         => $expiresAt,
            'is_active'          => true,
            'sent_by'            => $this->option('sent-by'),
        ]);

        $this->info("✅ Mensaje [{$type}] creado con éxito (ID: {$broadcast->id})");

        // Mostrar un resumen visual en consola
        $this->newLine();
        $this->line("  📢 <fg=yellow>Título:</> {$broadcast->title}");
        $this->line("  📝 <fg=yellow>Cuerpo:</>  {$broadcast->body}");
        $this->line("  🎨 <fg=yellow>Tipo:</>    {$type} (ícono: {$broadcast->effective_icon}, color: {$broadcast->effective_color})");
        if ($broadcast->action_url) {
            $this->line("  🔗 <fg=yellow>URL:</>     {$broadcast->action_url}");
        }
        if ($expiresAt) {
            $this->line("  ⏰ <fg=yellow>Expira:</>  {$expiresAt->toDateTimeString()}");
        }
        $this->newLine();

        // Dispatch el evento WebSocket
        try {
            event(new BroadcastSent($broadcast));
            $this->info("📡 WebSocket enviado al canal 'radar'. ¡Todos los dispositivos han recibido la señal!");
        } catch (\Exception $e) {
            $this->warn("⚠️ Mensaje guardado, pero no se pudo enviar el WebSocket.");
            $this->line("   Asegúrate de tener activo: php artisan reverb:start");
            $this->line("   Error: " . $e->getMessage());
        }

        // Dispatch Firebase Push nativo chunkeado
        if (!$this->option('no-push')) {
            $chunkSize = max(50, (int) $this->option('chunk'));

            // Obtener IDs de todos los usuarios con tokens FCM registrados
            $userIds = User::whereHas('fcmTokens')->pluck('id')->all();
            $totalUsers = count($userIds);

            if ($totalUsers === 0) {
                $this->warn('ℹ️  Sin suscriptores Firebase registered todavía.');
            } else {
                $chunks = array_chunk($userIds, $chunkSize);
                $totalChunks = count($chunks);

                // Despachar cada chunk con delay escalonado (1 seg entre chunks)
                foreach ($chunks as $index => $chunk) {
                    \App\Jobs\SendMassFirebaseChunk::dispatch(
                        userIds:   $chunk,
                        title:     $this->argument('title'),
                        body:      strip_tags($body),
                        icon:      $this->option('icon') ? '/icon.png' : null,
                        actionUrl: $this->option('action-url'),
                        image:     $this->option('image'),
                    )->delay(now()->addSeconds($index * 1));
                }

                $this->info("📲 Firebase Push encolado: {$totalUsers} suscriptores en {$totalChunks} chunks de {$chunkSize}.");
                $this->line("   Cola procesada por el worker sin saturar el VPS (vía Firebase) ✅");
            }
        }

        return Command::SUCCESS;
    }
}
