<?php
namespace App\Services;

use App\Models\Report;
use App\Models\Media;
use App\Events\ReportCreated;
use App\Events\ReportStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DenunciaService
{
    public function obtenerDenuncias($usuario)
    {
        // Si tiene permiso para gestionar, ve todas
        if ($usuario->can('gestionar denuncias')) {
            return Report::with(['category', 'user', 'media'])->latest()->paginate(15);
        }

        // Todos los usuarios pueden ver todos los reportes en el mapa y radar
        return Report::with(['category', 'user', 'media'])
            ->latest()
            ->paginate(15);
    }

    public function crearDenuncia(array $data): Report
    {
        return DB::transaction(function () use ($data) {
            // Extraer fotos antes de crear el reporte
            $fotos = $data['fotos'] ?? [];
            unset($data['fotos']);

            // Asignar usuario y estado inicial
            // user_id viene del controller ($request->user()->id) para compatibilidad con Sanctum
            if (!isset($data['user_id'])) {
                $data['user_id'] = Auth::id();
            }
            $data['status'] = 'pendiente';

            // Crear el reporte
            $report = Report::create($data);

            // Guardar las fotos (máximo 5)
            if (!empty($fotos)) {
                $this->guardarFotos($report, $fotos);
            }

            // Cargar relaciones para el broadcast
            $report->load(['media', 'category', 'user']);

            // Emitir evento para actualizar el mapa en tiempo real
            broadcast(new ReportCreated($report))->toOthers();

            return $report;
        });
    }

    /**
     * Guarda las fotos de evidencia
     */
    private function guardarFotos(Report $report, array $fotos): void
    {
        // Limitar a 5 fotos máximo
        $fotos = array_slice($fotos, 0, 5);

        foreach ($fotos as $foto) {
            if ($foto && $foto->isValid()) {
                // Guardar con nombre único
                $path = $foto->store('evidencias/' . date('Y/m'), 'public');
                
                Media::create([
                    'report_id' => $report->id,
                    'file_path' => $path,
                ]);
            }
        }
    }

    public function actualizarEstado(Report $report, string $nuevoEstado)
    {
        $estadoAnterior = $report->status;
        $report->update(['status' => $nuevoEstado]);
        
        // Emitir evento para actualizar el color del marcador en tiempo real
        broadcast(new ReportStatusChanged($report, $estadoAnterior))->toOthers();

        // Notificar al dueño del reporte sobre el cambio de estado
        $statusLabels = [
            'pendiente' => 'Pendiente',
            'en_revision' => 'En revisión',
            'atendido' => 'Atendido',
            'desestimado' => 'Desestimado',
        ];

        $newLabel = $statusLabels[$nuevoEstado] ?? $nuevoEstado;

        $notification = \App\Models\Notification::create([
            'user_id' => $report->user_id,
            'report_id' => $report->id,
            'type' => 'status_change',
            'title' => 'Estado de tu denuncia actualizado',
            'message' => "Tu denuncia \"{$report->title}\" cambió a: {$newLabel}.",
            'data' => [
                'old_status' => $estadoAnterior,
                'new_status' => $nuevoEstado,
                'report_title' => $report->title,
            ],
        ]);

        event(new \App\Events\NotificationCreated($notification));
        
        return $report;
    }
}