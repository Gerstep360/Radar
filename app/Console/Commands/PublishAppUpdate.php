<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishAppUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update 
                            {version : La versión de la app (ej: 1.2.0)} 
                            {notes : Novedades de esta versión} 
                            {file_path : Ruta al archivo APK relativo a storage/app/public (ej: versions/1.2.0/app.apk)} 
                            {--force : Si la actualización es obligatoria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publica una nueva versión de la app localmente, y notifica a los clientes en tiempo real.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');
        $notes = $this->argument('notes');
        $filePath = $this->argument('file_path');
        $isForce = $this->option('force');

        // Validar que el archivo existe en el Storage public
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $this->error("El archivo no existe en storage/app/public/{$filePath}.");
            $this->newLine();
            $this->warn("Asegúrate de subir tu archivo a esa ruta primero.");
            return \Illuminate\Console\Command::FAILURE;
        }

        // Desactivar las actualizaciones anteriores
        \App\Models\AppUpdate::where('is_active', true)->update(['is_active' => false]);

        // Crear la nueva actualización
        $update = \App\Models\AppUpdate::create([
            'version' => $version,
            'release_notes' => $notes,
            'file_path' => $filePath,
            'is_active' => true,
            'force_update' => $isForce,
        ]);

        $this->info("Actualización {$version} registrada con éxito.");

        // Despachar el evento websocket
        event(new \App\Events\AppUpdatePublished($update));

        $this->info("WebSocket despachado al canal público 'radar'.");
        $this->line("URL pública de descarga: " . $update->download_url);

        return \Illuminate\Console\Command::SUCCESS;
    }
}
