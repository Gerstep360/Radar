<?php
namespace App\Services;

use App\Models\Report;
use App\Models\Media;
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

        // Ciudadanos solo ven las suyas
        return Report::where('user_id', $usuario->id)
            ->with(['category', 'media'])
            ->latest()
            ->paginate(10);
    }

    public function crearDenuncia(array $data): Report
    {
        return DB::transaction(function () use ($data) {
            // Extraer fotos antes de crear el reporte
            $fotos = $data['fotos'] ?? [];
            unset($data['fotos']);

            // Asignar usuario y estado inicial
            $data['user_id'] = Auth::id();
            $data['status'] = 'pendiente';

            // Crear el reporte
            $report = Report::create($data);

            // Guardar las fotos (máximo 5)
            if (!empty($fotos)) {
                $this->guardarFotos($report, $fotos);
            }

            return $report->load('media');
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
        $report->update(['status' => $nuevoEstado]);
        // Aquí podrías disparar notificaciones al usuario: "Tu denuncia fue atendida"
        return $report;
    }
}