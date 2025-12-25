<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Permisos y roles primero
            PermissionSeeder::class,
            
            // 2. Usuarios (admin, funcionario, ciudadanos)
            UserSeeder::class,
            
            // 3. Categorías de reportes
            CategoriaSeeder::class,
            
            // 4. Reportes con diferentes estados
            ReportSeeder::class,
            
            // 5. Media/fotos de reportes
            MediaSeeder::class,
            
            // 6. Votos (genera votos para probar colores)
            VoteSeeder::class,
            
            // 7. Comentarios en reportes
            CommentSeeder::class,
            
            // 8. Historial de cambios
            ReportLogSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🚀 Base de datos poblada correctamente');
        $this->command->info('');
        $this->command->info('📊 Colores de marcadores:');
        $this->command->info('   🔴 Rojo (pulsante) = Seguridad/Emergencia o +10 votos');
        $this->command->info('   🟡 Amarillo = Pendiente');
        $this->command->info('   🔵 Azul = En proceso');
        $this->command->info('   🟢 Verde = Resuelto');
        $this->command->info('   ⚫ Gris = Descartado');
        $this->command->info('');
        $this->command->info('👤 Usuario admin: admin@radar.test / password');
        $this->command->info('👤 Usuario funcionario: funcionario@radar.test / password');
    }
}
