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

        // data tenants
        $tenatId = DB::table('tenats')->updateOrInsert(
            ['nama_tenant' => 'Kantin sejahtera'],
            [
                'nama_tenat' => 'Kantin sejahtera',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


        // ambil ID tenant
        $tenantId = DB::table('tenants')
        ->where('nama_tenant', 'Kantin sejahtera')
        ->value('id');


        // membuat akun admin tenant
        DB::table('users')->updateOrInsert(
            ['username' => 'admin_tenant'], // Patokan pengecekan
            [
                'tenant_id' => $tenantId,
                'nama' => 'Admin Tenant',
                'email' => 'admin@test.test',
                'password' => Md5Password::make('123'),
                'role' => 'tenant_admin',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
