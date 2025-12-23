<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    // Desactivamos updated_at porque un log es historia, no se edita.
    public $timestamps = false; 

    protected $fillable = ['report_id', 'admin_id', 'comment', 'created_at'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // El admin que hizo el cambio
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}