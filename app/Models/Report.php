<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 
        'category_id', 
        'title', 
        'description', 
        'latitude', 
        'longitude', 
        'status'
    ];

    // Casteo para que no tengas problemas con decimales en el JSON
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // Pertenece a un ciudadano
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Tiene muchas fotos
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    // Tiene un historial de cambios
    public function logs()
    {
        return $this->hasMany(ReportLog::class);
    }

    // Tiene muchos votos
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Verificar si el usuario actual ya votó
    public function hasVotedBy($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }
}
