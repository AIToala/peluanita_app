<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.create',
            'users.edit',
            'users.delete',
            'users.view',
            'users.index',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.view',
            'roles.index',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'permissions.view',
            'permissions.index',
            'clientes.create',
            'clientes.edit',
            'clientes.delete',
            'clientes.view',
            'clientes.index',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'employees.view',
            'employees.index',
            'citas.create',
            'citas.edit',
            'citas.delete',
            'citas.view',
            'citas.index',
            'atencion.create',
            'atencion.edit',
            'atencion.delete',
            'atencion.view',
            'atencion.index',
            'servicios.create',
            'servicios.edit',
            'servicios.delete',
            'servicios.view',
            'servicios.index',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }


        $admin = Role::create(['name' => 'admin']);
        $client = Role::create(['name' => 'cliente']);
        $employee = Role::create(['name' => 'empleado']);


        $admin->givePermissionTo(Permission::all());

        $client->givePermissionTo([
            'citas.create',
            'citas.edit',
            'citas.delete',
            'citas.view',
            'citas.index',
        ]);

        $employee->givePermissionTo([
            'citas.create',
            'citas.edit',
            'citas.delete',
            'citas.view',
            'citas.index',
            'atencion.create',
            'atencion.edit',
            'atencion.delete',
            'atencion.view',
            'atencion.index',
            'servicios.create',
            'servicios.edit',
            'servicios.delete',
            'servicios.view',
            'servicios.index',
        ]);
    }
}
