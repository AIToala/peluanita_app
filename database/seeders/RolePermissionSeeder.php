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
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'manage_clients',
            'manage_employees',
            'request_appointments',
            'create_appointments',
            'cancel_appointments',
            'manage_appointments',
            'manage_services',
            'manage_attentions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }


        $admin = Role::create(['name' => 'admin']);
        $client = Role::create(['name' => 'cliente']);
        $employee = Role::create(['name' => 'empleado']);


        $admin->givePermissionTo(Permission::all());

        $client->givePermissionTo([
            'request_appointments',
            'cancel_appointments',
        ]);

        $employee->givePermissionTo([
            'create_appointments',
            'cancel_appointments',
            'manage_appointments',
            'manage_attentions',
            'manage_clients',
        ]);
    }
}
