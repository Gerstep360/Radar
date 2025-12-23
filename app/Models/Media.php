<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute; // <--- No olvides importar esto arriba

class Media extends Model
{
    protected $fillable = ['report_id', 'file_path'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Obtiene la URL pública de la foto
     */

    protected function url(): Attribute
    {
        return Attribute::make(
            // Simplemente devolvemos el 'path'. 
            // Si por alguna razón es null, devolvemos cadena vacía para que no explote.
            get: fn ($value, $attributes) => $attributes['path'] ?? '',
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