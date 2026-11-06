<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Support\Security\Md5Password;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Kita pakai updateOrInsert agar kalau dijalankan 2x tidak error "Duplicate Entry"
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'], // Patokan pengecekan
            [
                'tenant_id' => null,
                'nama' => 'Super Admin',
                'email' => 'admin@kantin.test', 
                'password' => Md5Password::make('123'), // PASSWORD SANGAT SIMPEL: 123
                'role' => 'super_admin',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
