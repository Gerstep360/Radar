<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUpdate extends Model
{
    protected $fillable = [
        'version',
        'release_notes',
        'file_path',
        'is_active',
        'force_update',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'force_update' => 'boolean',
    ];

    /**
     * Get the public URL for downloading the app.
     */
    public function getDownloadUrlAttribute(): string
    {
        // Si hay una petición HTTP, usamos ese host. Si es por consola, usamos APP_URL.
        try {
            if (request()->header('Host')) {
                return request()->getSchemeAndHttpHost() . '/api/app/download';
            }
        } catch (\Throwable $e) {}

        return rtrim(config('app.url', 'http://localhost'), '/') . '/api/app/download';
    }
}
