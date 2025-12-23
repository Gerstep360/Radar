<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            // --- INFRAESTRUCTURA VIAL ---
            [
                'name' => 'Baches y Pavimento',
                'description' => 'Huecos en asfalto, losetas levantadas o daños en la vía.',
                'priority' => 5, // Muy Alta
            ],
            [
                'name' => 'Mantenimiento de Calles (Tierra)',
                'description' => 'Necesidad de raspado, nivelación o calles intransitables por lluvia.',
                'priority' => 3,
            ],
            
            // --- SERVICIOS PÚBLICOS ---
            [
                'name' => 'Alumbrado Público',
                'description' => 'Focos quemados, postes caídos o zonas oscuras peligrosas.',
                'priority' => 4,
            ],
            [
                'name' => 'Basura y Limpieza',
                'description' => 'Microbasurales, falta de recojo o contenedores desbordados.',
                'priority' => 4,
            ],

            // --- MEDIO AMBIENTE Y SALUD ---
            [
                'name' => 'Lotes Baldíos / Monte Alto',
                'description' => 'Propiedades abandonadas con maleza (riesgo de dengue/seguridad).',
                'priority' => 4,
            ],
            [
                'name' => 'Quemas y Humo',
                'description' => 'Quema de basura, chaqueos ilegales o contaminación del aire.',
                'priority' => 5, // Urgente por salud
            ],
            [
                'name' => 'Drenaje y Canales',
                'description' => 'Canales obstruidos, agua estancada o desbordes.',
                'priority' => 3,
            ],

            // --- ORDEN Y SEGURIDAD ---
            [
                'name' => 'Ruidos Molestos',
                'description' => 'Fiestas fuera de horario, rockolas, escapes libres.',
                'priority' => 2,
            ],
            [
                'name' => 'Animales Sueltos / Peligrosos',
                'description' => 'Ganado en la vía, perros agresivos o fauna silvestre en riesgo.',
                'priority' => 3,
            ],
            
            // --- LA VÁLVULA DE ESCAPE ---
            [
                'name' => 'Otros / Incidente General',
                'description' => 'Cualquier situación no listada arriba.',
                'priority' => 1,
            ],
        ];

        foreach ($categorias as $cat) {
            // Usamos updateOrCreate para poder correr el seeder varias veces sin duplicar
            // y actualizar descripciones si las cambias.
            Category::updateOrCreate(
                ['name' => $cat['name']], 
                [
                    'description' => $cat['description'],
                    'priority' => $cat['priority']
                ]
            );
        }
    }
}