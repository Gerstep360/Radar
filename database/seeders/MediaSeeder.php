<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Report;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $reports = Report::all();

        foreach ($reports as $report) {
            // Creamos 1 o 2 fotos por reporte
            Media::create([
                'report_id' => $report->id,
                'file_path' => 'evidencias/foto_' . $report->id . '_a.jpg',
            ]);

            // Al 50% le agregamos una segunda foto
            if (rand(0, 1)) {
                Media::create([
                    'report_id' => $report->id,
                    'file_path' => 'evidencias/foto_' . $report->id . '_b.jpg',
                ]);
            }
        }
    }
}