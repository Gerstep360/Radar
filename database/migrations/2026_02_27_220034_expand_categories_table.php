<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Emergencia de Salud', 'description' => 'Situaciones que requieren asistencia médica inmediata.', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Incendio / Fuego', 'description' => 'Fuego descontrolado o riesgo inminente de incendio.', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fuga de Gas', 'description' => 'Olor a gas o rotura de tuberías de gas.', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cableado Suelto / Caído', 'description' => 'Cables eléctricos o de servicios en la calle.', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inundación / Desborde', 'description' => 'Acumulación de agua que impide el tránsito o afecta viviendas.', 'priority' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Árbol Caído / Riesgo', 'description' => 'Árboles obstruyendo la vía o con peligro de caer.', 'priority' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Infraestructura Dañada', 'description' => 'Puentes, muros o aceras con daños estructurales.', 'priority' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Obstáculo en Vía', 'description' => 'Escombros o objetos que bloquean el tránsito.', 'priority' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Plagas / Vectores', 'description' => 'Proliferación de insectos o animales que afectan la salud.', 'priority' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Señalización Faltante', 'description' => 'Falta de señales de tránsito o semáforos dañados.', 'priority' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Comercio Informal / Obstrucción', 'description' => 'Ventas que bloquean el paso peatonal.', 'priority' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maltrato Animal', 'description' => 'Situaciones de crueldad o abandono animal.', 'priority' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')->whereIn('name', [
            'Emergencia de Salud', 'Incendio / Fuego', 'Fuga de Gas', 'Cableado Suelto / Caído',
            'Inundación / Desborde', 'Árbol Caído / Riesgo', 'Infraestructura Dañada',
            'Obstáculo en Vía', 'Plagas / Vectores', 'Señalización Faltante',
            'Comercio Informal / Obstrucción', 'Maltrato Animal'
        ])->delete();
    }
};
