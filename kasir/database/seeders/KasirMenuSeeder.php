<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kasir;
use App\Models\Menu;
use Illuminate\Support\Facades\Hash;

class KasirMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kasir::updateOrCreate(
            ['username' => 'Kasir01'],
            [
                'nama_kasir' => 'Kasir 01',
                'password' => Hash::make('password123'),
            ]
        );

        $menus = [
            [
                'nama_menu' => 'Nasi Goreng',
                'kategori' => 'Makanan',
                'harga' => 15000,
                'stok' => 10,
            ],
            [
                'nama_menu' => 'Mie Ayam',
                'kategori' => 'Makanan',
                'harga' => 12000,
                'stok' => 15,
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'kategori' => 'Minuman',
                'harga' => 5000,
                'stok' => 20,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::firstOrCreate(
                ['nama_menu' => $menu['nama_menu']],
                $menu
            );
        }
    }
}
