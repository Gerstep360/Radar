<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute; // <--- No olvides importar esto arriba

class Media extends Model
{
    protected $fillable = ['report_id', 'file_path'];
    protected $appends = ['url'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Obtiene la URL pública de la foto.
     * Usa url() de config para consistencia (APP_URL).
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (empty($attributes['file_path'])) {
                    return '';
                }

                // Intentar usar el host del request actual (funciona tanto para localhost como IP de red)
                try {
                    $request = request();
                    if ($request) {
                        return $request->getSchemeAndHttpHost() . '/storage/' . $attributes['file_path'];
                    }
                } catch (\Throwable $e) {
                    // fallback
                }

                return Storage::disk('public')->url($attributes['file_path']);
            },
        );
    }

    /**
     * Verifica si el archivo existe
     */
    public function exists(): bool
    {
        return Storage::disk('public')->exists($this->file_path);
    }
}