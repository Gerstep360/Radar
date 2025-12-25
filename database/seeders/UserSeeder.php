<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin del sistema
        $admin = User::firstOrCreate(
            ['email' => 'admin@radar.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Asignar rol admin si existe Spatie
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('admin');
        }

        // Funcionario/Moderador municipal
        $moderador = User::firstOrCreate(
            ['email' => 'moderador@radar.test'],
            [
                'name' => 'Juan Pérez (Moderador)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (method_exists($moderador, 'assignRole')) {
            $moderador->assignRole('moderador');
        }

        // Ciudadanos de prueba para votos
        $ciudadanos = [
            ['name' => 'María García', 'email' => 'maria@test.com'],
            ['name' => 'Carlos López', 'email' => 'carlos@test.com'],
            ['name' => 'Ana Rodríguez', 'email' => 'ana@test.com'],
            ['name' => 'Pedro Martínez', 'email' => 'pedro@test.com'],
            ['name' => 'Laura Sánchez', 'email' => 'laura@test.com'],
            ['name' => 'Diego Fernández', 'email' => 'diego@test.com'],
            ['name' => 'Sofía Morales', 'email' => 'sofia@test.com'],
            ['name' => 'Roberto Vargas', 'email' => 'roberto@test.com'],
            ['name' => 'Carmen Flores', 'email' => 'carmen@test.com'],
            ['name' => 'Miguel Rojas', 'email' => 'miguel@test.com'],
            ['name' => 'Patricia Herrera', 'email' => 'patricia@test.com'],
            ['name' => 'Fernando Castro', 'email' => 'fernando@test.com'],
            ['name' => 'Lucía Mendoza', 'email' => 'lucia@test.com'],
            ['name' => 'Andrés Paredes', 'email' => 'andres@test.com'],
            ['name' => 'Valentina Suárez', 'email' => 'valentina@test.com'],
            ['name' => 'Javier Ortega', 'email' => 'javier@test.com'],
            ['name' => 'Gabriela Ramos', 'email' => 'gabriela@test.com'],
            ['name' => 'Ricardo Peña', 'email' => 'ricardo@test.com'],
            ['name' => 'Isabel Núñez', 'email' => 'isabel@test.com'],
            ['name' => 'Alejandro Vega', 'email' => 'alejandro@test.com'],
        ];

        foreach ($ciudadanos as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('ciudadano');
            }
        }

        $this->command->info('✅ Se crearon ' . (count($ciudadanos) + 2) . ' usuarios de prueba');
    }
}
