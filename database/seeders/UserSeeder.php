<?php

namespace Database\Seeders;

use App\Models\User; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@peluanita.com',
            'password' => bcrypt('peluanitaadmin'),
            'username' => 'admin',
            'role' => 'admin',
            'estado' => 1,
            'email_verified_at' => now(),            
        ])->assignRole('admin');
    }
}
