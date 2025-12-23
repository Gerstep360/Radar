<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primero crear permisos y roles
        $this->call([
            PermissionSeeder::class,
            users::class,
            CategoriaSeeder::class, // Categorías base
            ReportSeeder::class,    // Las denuncias
            MediaSeeder::class,     // Las fotos de las denuncias
            ReportLogSeeder::class, // El historial
        ]);


    }
}
