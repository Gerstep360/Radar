<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->buildUrl($request),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Genera la URL de la imagen usando el host del request entrante.
     * Así funciona tanto en localhost como en IP de red (para Tauri).
     */
    private function buildUrl(Request $request): string
    {
        if (empty($this->file_path)) {
            return '';
        }

        // Si ya es una URL completa, no añadir nada
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }

        // Usar el host del request para que las imágenes carguen en Tauri
        // manteniendo la independencia de la IP.
        return $request->getSchemeAndHttpHost() . '/storage/' . $this->file_path;
    }
}
