<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\AppUpdate::create([
            'version' => '1.0.1',
            'release_notes' => 'Actualización de prueba: mejoras y correcciones.',
            'file_path' => 'https://tuservidor.com/app/radar.apk', // O la ruta real si usas storage
            'is_active' => true,
            'force_update' => false,
        ]);
    }
}
