<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('12345678'),
        ]);
        $admin->assignRole('admin');

        $moderador = User::firstOrCreate([
            'email' => 'moderador@gmail.com',
        ], [
            'name' => 'Moderador',
            'password' => bcrypt('12345678'),
        ]);
        $moderador->assignRole('moderador');

        $ciudadano = User::firstOrCreate([
            'email' => 'ciudadano@gmail.com',
        ], [
            'name' => 'Ciudadano',
            'password' => bcrypt('12345678'),
        ]);
        $ciudadano->assignRole('ciudadano');
    }
}
