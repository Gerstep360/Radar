<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::firstOrCreate(['name' => 'ver denuncias']);
        Permission::firstOrCreate(['name' => 'crear denuncias']);
        Permission::firstOrCreate(['name' => 'gestionar denuncias']);
        Permission::firstOrCreate(['name' => 'eliminar denuncias']);

        // Crear roles y asignar permisos
        $ciudadano = Role::firstOrCreate(['name' => 'ciudadano']);
        $ciudadano->givePermissionTo(['ver denuncias', 'crear denuncias']);

        $moderador = Role::firstOrCreate(['name' => 'moderador']);
        $moderador->givePermissionTo(['ver denuncias', 'gestionar denuncias']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
