<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    /**
     * Coordenadas base de La Guardia, Santa Cruz
     * Centro aproximado: -17.8935, -63.3245
     */
    private float $baseLat = -17.8935;
    private float $baseLng = -63.3245;

    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $defaultUser = $users[0] ?? 1;
        
        // Obtener categorías
        $categories = Category::pluck('id', 'name')->toArray();
        
        // ============================================
        // 🔴 REPORTES URGENTES (Seguridad/Emergencia)
        // Para probar el color ROJO con pulso
        // ============================================
        $urgentReports = [
            [
                'category' => 'Seguridad Ciudadana',
                'title' => 'Asalto en parada de micro',
                'description' => 'Ayer a las 22:00 asaltaron a una señora en la parada del micro línea 73. Es la tercera vez esta semana. Necesitamos más patrullaje.',
                'status' => 'pendiente',
                'days_ago' => 1,
                'offset' => [0.002, 0.001],
            ],
            [
                'category' => 'Seguridad Ciudadana', 
                'title' => 'Motochorros merodeando por la zona',
                'description' => 'Hay dos motos sin placa que están dando vueltas por el barrio, ya intentaron arrebatar celulares dos veces.',
                'status' => 'en_revision',
                'days_ago' => 0,
                'offset' => [-0.003, 0.002],
            ],
            [
                'category' => 'Quemas y Humo',
                'title' => '🔥 CHAQUEO FUERA DE CONTROL',
                'description' => 'Están quemando un terreno grande cerca del colegio, el humo está llegando a las casas. Los niños no pueden salir.',
                'status' => 'pendiente',
                'days_ago' => 0,
                'offset' => [0.004, -0.003],
            ],
        ];

        // ============================================
        // 🟡 REPORTES PENDIENTES
        // Para probar el color AMARILLO
        // ============================================
        $pendingReports = [
            [
                'category' => 'Baches y Pavimento',
                'title' => 'Bache profundo en Av. Principal',
                'description' => 'El bache tiene como 30cm de profundidad, ya reventaron varias llantas. Está justo en el carril de la derecha.',
                'status' => 'pendiente',
                'days_ago' => 3,
                'offset' => [0.001, 0.002],
            ],
            [
                'category' => 'Alumbrado Público',
                'title' => 'Cuadra completa sin luz',
                'description' => 'Los 4 postes de la cuadra están sin luz hace una semana. Es muy peligroso de noche.',
                'status' => 'pendiente',
                'days_ago' => 7,
                'offset' => [-0.002, -0.001],
            ],
            [
                'category' => 'Basura y Limpieza',
                'title' => 'Microbasural en esquina',
                'description' => 'La gente está botando basura en la esquina del lote baldío. Ya hay ratas y el olor es insoportable.',
                'status' => 'pendiente',
                'days_ago' => 5,
                'offset' => [0.003, 0.001],
            ],
            [
                'category' => 'Lotes Baldíos / Monte Alto',
                'title' => 'Monte alto con víboras',
                'description' => 'El lote abandonado tiene el monte altísimo, los vecinos vieron víboras saliendo. Hay niños en la zona.',
                'status' => 'pendiente',
                'days_ago' => 10,
                'offset' => [-0.001, 0.003],
            ],
            [
                'category' => 'Drenaje y Canales',
                'title' => 'Canal tapado, se inunda la calle',
                'description' => 'Cada vez que llueve se inunda todo el barrio porque el canal está lleno de basura y tierra.',
                'status' => 'pendiente',
                'days_ago' => 14,
                'offset' => [0.002, -0.002],
            ],
        ];

        // ============================================
        // 🔵 REPORTES EN REVISIÓN
        // Para probar el color AZUL
        // ============================================
        $inProgressReports = [
            [
                'category' => 'Baches y Pavimento',
                'title' => 'Reparación de pavimento Calle 5',
                'description' => 'Ya vinieron a ver, dijeron que esta semana arreglan. Ojalá sea verdad.',
                'status' => 'en_revision',
                'days_ago' => 4,
                'offset' => [-0.004, 0.001],
            ],
            [
                'category' => 'Mantenimiento de Calles (Tierra)',
                'title' => 'Esperando motoniveladora',
                'description' => 'Nos dijeron que la motoniveladora viene el viernes a nivelar toda la calle.',
                'status' => 'en_revision',
                'days_ago' => 2,
                'offset' => [0.001, -0.003],
            ],
            [
                'category' => 'Alumbrado Público',
                'title' => 'Poste dañado - En reparación',
                'description' => 'CRE ya vino a revisar, dice que mañana traen el material para reparar.',
                'status' => 'en_revision',
                'days_ago' => 1,
                'offset' => [-0.002, 0.004],
            ],
        ];

        // ============================================
        // 🟢 REPORTES ATENDIDOS
        // Para probar el color VERDE
        // ============================================
        $resolvedReports = [
            [
                'category' => 'Baches y Pavimento',
                'title' => 'Bache reparado ✓',
                'description' => 'Vinieron ayer y lo arreglaron. Quedó bien, gracias por la gestión.',
                'status' => 'atendido',
                'days_ago' => 1,
                'offset' => [0.005, 0.002],
            ],
            [
                'category' => 'Basura y Limpieza',
                'title' => 'Limpiaron el microbasural',
                'description' => 'Mandaron cuadrilla y limpiaron todo. Ahora hay que cuidar que no vuelvan a ensuciar.',
                'status' => 'atendido',
                'days_ago' => 3,
                'offset' => [-0.003, -0.002],
            ],
            [
                'category' => 'Alumbrado Público',
                'title' => 'Focos nuevos instalados',
                'description' => 'Cambiaron todos los focos de la cuadra, ahora está bien iluminado.',
                'status' => 'atendido',
                'days_ago' => 5,
                'offset' => [0.002, 0.003],
            ],
            [
                'category' => 'Ruidos Molestos',
                'title' => 'Problema de ruido solucionado',
                'description' => 'La alcaldía habló con los dueños de la quinta y ya respetan el horario.',
                'status' => 'atendido',
                'days_ago' => 7,
                'offset' => [-0.001, -0.004],
            ],
        ];

        // ============================================
        // ⚫ REPORTES DESESTIMADOS
        // Para probar el color GRIS
        // ============================================
        $discardedReports = [
            [
                'category' => 'Ruidos Molestos',
                'title' => 'Queja sin fundamento',
                'description' => 'Se verificó y no había ruido excesivo, era dentro del horario permitido.',
                'status' => 'desestimado',
                'days_ago' => 10,
                'offset' => [0.004, -0.001],
            ],
            [
                'category' => 'Baches y Pavimento',
                'title' => 'Reporte duplicado',
                'description' => 'Este bache ya fue reportado anteriormente y está en proceso.',
                'status' => 'desestimado',
                'days_ago' => 8,
                'offset' => [-0.005, 0.003],
            ],
        ];

        // Crear todos los reportes
        $allReports = array_merge(
            $urgentReports,
            $pendingReports,
            $inProgressReports,
            $resolvedReports,
            $discardedReports
        );

        foreach ($allReports as $reportData) {
            $categoryId = $categories[$reportData['category']] ?? $categories['Baches y Pavimento'] ?? 1;
            
            Report::create([
                'user_id' => $users[array_rand($users)] ?? $defaultUser,
                'category_id' => $categoryId,
                'title' => $reportData['title'],
                'description' => $reportData['description'],
                'latitude' => $this->baseLat + $reportData['offset'][0],
                'longitude' => $this->baseLng + $reportData['offset'][1],
                'status' => $reportData['status'],
                'created_at' => Carbon::now()->subDays($reportData['days_ago']),
                'updated_at' => Carbon::now()->subDays($reportData['days_ago']),
            ]);
        }

        $this->command->info('✅ Se crearon ' . count($allReports) . ' reportes de prueba');
    }
}
