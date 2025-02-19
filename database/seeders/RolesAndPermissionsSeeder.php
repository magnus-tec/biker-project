<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'roles' => ['ver-rol', 'crear-rol', 'actualizar-rol', 'eliminar-rol'],
            'permissions' => ['ver-permisos', 'agregar-permisos', 'actualizar-permisos', 'eliminar-permisos'],
            'users' => ['ver-trabajadores', 'actualizar-trabajadores', 'eliminar-trabajadores', 'agregar-trabajadores', 'buscar-trabajadores'],
            'permisos-mecanicos' => ['ver-servicios'],
            'permisos-ventas' => ['actualizar-mecanicos', 'eliminar-mecanicos', 'agregar-mecanicos', 'buscar-mecanicos', 'ver-mecanicos', 'ver-vehiculos', 'actualizar-vehiculos', 'eliminar-vehiculos', 'agregar-vehiculos', 'buscar-vehiculos', 'registro-conductores', 'actualizar-conductores', 'eliminar-conductores', 'agregar-conductores', 'buscar-conductores', 'ver-conductores', 'filtrar-por-trabajador-servicios', 'filtrar-por-estado-servicios', 'agregar-servicios', 'ver-garantias', 'actualizar-garantias', 'eliminar-garantias', 'agregar-garantias', 'buscar-garantias', 'ver-productos', 'agregar-productos', 'actualizar-productos', 'eliminar-productos', 'buscar-productos'],
        ];

        foreach ($permissions as $category => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }
        $roles = [
            'administrador' => array_merge(...array_values($permissions)), // Admin tiene todos los permisos
            'mecanico' => array_merge($permissions['permisos-mecanicos']),
            'ventas' => array_merge($permissions['permisos-ventas'], $permissions['permisos-mecanicos']),
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $validPermissions = Permission::whereIn('name', $rolePermissions)->pluck('name')->toArray();
            foreach ($validPermissions as $perm) {
                if (!$role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                }
            }
        }
        $users = [
            [
                'name' => 'administrador',
                'email' => 'administrador@gmail.com',
                'password' => '12345678',
                'role' => 'administrador'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password']
                ]
            );
            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
