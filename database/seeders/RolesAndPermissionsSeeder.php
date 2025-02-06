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
            'roles' => ['view-role', 'create-role', 'update-role', 'delete-role'],
            'permissions' => ['view-permission', 'create-permission', 'update-permission', 'delete-permission'],
            'users' => ['view-user', 'create-user', 'update-user', 'delete-user'],
            'customers' => ['registro-clientes', 'actualizar-clientes', 'eliminar-clientes', 'agregar-clientes', 'buscar-clientes'],
            'mechanics' => ['registro-mecanicos', 'actualizar-mecanicos', 'eliminar-mecanicos', 'agregar-mecanicos', 'buscar-mecanicos', 'lista-trabajos-mecanicos'],
            'view-mechanics' => ['lista-trabajos'],
        ];

        foreach ($permissions as $category => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
        }
        $roles = [
            'admin' => array_merge(...array_values($permissions)), // Admin tiene todos los permisos
            'user' => array_merge($permissions['customers'], $permissions['mechanics']),
            'mechanic' => array_merge($permissions['view-mechanics']),
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
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => '12345678',
                'role' => 'admin'
            ],
            [
                'name' => 'mecanico',
                'email' => 'mecanico@gmail.com',
                'password' => '12345678',
                'role' => 'mechanic'
            ],
            [
                'name' => 'usuario',
                'email' => 'usuario@gmail.com',
                'password' => '12345678',
                'role' => 'user'
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
