<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppUpdate;
use App\Events\AppUpdatePublished;
use Illuminate\Support\Facades\Storage;

class UpdateApkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:apk {version : La versión de la app (ej: 2.0.0)}
                            {--notes=Nueva versión disponible con mejoras de rendimiento. : Novedades de esta versión}
                            {--force : Si la actualización es obligatoria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pública una nueva versión de la app. Busca automáticamente el APK en storage/app/public/app/version/{version}/';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');
        $notes = $this->option('notes');
        $isForce = $this->option('force');

        // Según requerimiento de la ruta: storage/public/app/version/$numerodeversion
        // En Laravel usando el disco "public" la base es storage/app/public, 
        // por lo tanto la ruta relativa dentro del disco es 'app/version/' . $version
        $directoryPath = 'app/version/' . $version;
        
        if (!Storage::disk('public')->exists($directoryPath)) {
            $this->error("Error: El directorio no existe.");
            $this->info("Ruta buscada: storage/app/public/{$directoryPath}");
            Storage::disk('public')->makeDirectory($directoryPath);
            $this->line("✅ He creado el directorio por ti. Por favor, coloca tu archivo .apk en esa carpeta y vuelve a ejecutar el comando.");
            return Command::FAILURE;
        }

        // Buscar el archivo apk dentro del directorio
        $files = Storage::disk('public')->files($directoryPath);
        $apkFile = null;
        
        foreach ($files as $file) {
            if (str_ends_with(strtolower($file), '.apk')) {
                $apkFile = $file;
                break;
            }
        }

        if (!$apkFile) {
            $this->error("No se encontró ningún archivo .apk en storage/app/public/{$directoryPath}.");
            $this->info("Asegúrate de colocar tu aplicación (ej: app.apk) dentro del directorio de la versión.");
            return Command::FAILURE;
        }

        // Desactivar las actualizaciones anteriores
        AppUpdate::where('is_active', true)->update(['is_active' => false]);

        // Activar la nueva o existente actualización
        $update = AppUpdate::updateOrCreate(
            ['version' => $version],
            [
                'release_notes' => $notes,
                'file_path' => $apkFile,
                'is_active' => true,
                'force_update' => $isForce,
            ]
        );

        $this->info("🎉 ¡Actualización {$version} registrada con éxito!");

        // Despachar el evento websocket (con try-catch por si el servidor WebSocket está apagado)
        try {
            event(new AppUpdatePublished($update));
            $this->info("📡 WebSocket despachado al canal público 'radar'. ¡Todos los clientes recibirán la señal!");
        } catch (\Exception $e) {
            $this->warn("⚠️ La actualización se guardó, pero no se pudo conectar al servidor WebSocket (Reverb) para avisar en tiempo real.");
            $this->line("Asegúrate de tener funcionando: php artisan reverb:start");
            $this->line("Error original: " . $e->getMessage());
        }

        $this->line("🔗 URL pública de descarga: " . $update->download_url);

        return Command::SUCCESS;
    }
}
