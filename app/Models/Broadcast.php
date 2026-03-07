<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'action_url',
        'image_url',
        'is_popup',
        'auto_close_seconds',
        'icon',
        'color',
        'expires_at',
        'is_active',
        'sent_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popup' => 'boolean',
        'auto_close_seconds' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Tipos de broadcast disponibles con su ícono y color por defecto.
     */
    const TYPES = [
        'notification' => ['icon' => 'bell',        'color' => '#4A90D9'],
        'announcement' => ['icon' => 'megaphone',    'color' => '#7C3AED'],
        'alert'        => ['icon' => 'triangle',     'color' => '#EF4444'],
        'update'       => ['icon' => 'download',     'color' => '#10B981'],
        'maintenance'  => ['icon' => 'wrench',       'color' => '#F59E0B'],
        'custom'       => ['icon' => 'star',         'color' => '#6B7280'],
    ];

    /**
     * Scope: solo broadcasts activos y no expirados.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Retorna el ícono efectivo: el personalizado o el default del tipo.
     */
    public function getEffectiveIconAttribute(): string
    {
        return $this->icon ?? (self::TYPES[$this->type]['icon'] ?? 'bell');
    }

    /**
     * Retorna el color efectivo: el personalizado o el default del tipo.
     */
    public function getEffectiveColorAttribute(): string
    {
        return $this->color ?? (self::TYPES[$this->type]['color'] ?? '#4A90D9');
    }
}
