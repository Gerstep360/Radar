<?php

namespace Database\Seeders;

use App\Models\ReportLog;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportLogSeeder extends Seeder
{
    public function run(): void
    {
        // Filtramos solo las denuncias que NO están pendientes (que ya se movieron)
        $reports = Report::where('status', '!=', 'pendiente')->get();
        $admin = User::first()->id ?? 1;

        foreach ($reports as $report) {
            
            // Log 1: Cuando se creó
            ReportLog::create([
                'report_id' => $report->id,
                'admin_id' => $report->user_id, // El dueño
                'comment' => 'Denuncia creada en el sistema.',
                'created_at' => $report->created_at,
            ]);

            // Log 2: Cuando cambió de estado (ej: a 'en_proceso')
            ReportLog::create([
                'report_id' => $report->id,
                'admin_id' => $admin, // El funcionario
                'comment' => 'Se asignó una cuadrilla para inspeccionar. Estado: ' . $report->status,
                'created_at' => $report->created_at->addHours(4),
            ]);
        }
    }
}