<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // IDs útiles
        $user1 = User::first()->id ?? 1; // Admin o primer user
        $catBache = Category::where('name', 'Baches y Pavimento')->first()->id;
        $catMonte = Category::where('name', 'Lotes Baldíos / Monte Alto')->first()->id;
        $catRuido = Category::where('name', 'Ruidos Molestos')->first()->id;

        $denuncias = [
            [
                'user_id' => $user1,
                'category_id' => $catBache,
                'title' => 'Cráter en la Doble Vía Km 9',
                'description' => 'Hay un bache enorme justo en el retorno del km 9, ya vi dos trufis reventar llanta ahí. Es peligroso de noche.',
                'latitude' => -17.8845, 
                'longitude' => -63.3150, // Cerca de la carretera principal
                'status' => 'pendiente',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $user1,
                'category_id' => $catMonte,
                'title' => 'Monte alto en Barrio Las Lomas',
                'description' => 'El lote de la esquina está que parece selva, sale olor feo y hay full mosquitos. El dueño no aparece hace meses.',
                'latitude' => -17.8910,
                'longitude' => -63.3220, // Zona más residencial
                'status' => 'en_revision',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $user1,
                'category_id' => $catRuido,
                'title' => 'Fiesta en quinta privada sin control',
                'description' => 'Están con música a todo volumen desde el viernes, no dejan dormir a los vecinos de la cuadra. Parece que es alquiler.',
                'latitude' => -17.8790,
                'longitude' => -63.3080, // Zona de quintas
                'status' => 'atendido',
                'created_at' => Carbon::now()->subWeek(),
            ],
        ];

        foreach ($denuncias as $data) {
            Report::create($data);
        }
    }
}