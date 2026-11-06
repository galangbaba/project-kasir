<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private function user(array $a, string $role, ?int $tenantId): User
    {
        $u = new User(['nama' => $a['nama'], 'email' => $a['email'] ?? null, 'username' => $a['username'], 'status' => $a['status'] ?? 'aktif']);
        // Password sangat simpel: 123
        $u->forceFill(['role' => $role, 'tenant_id' => $tenantId, 'password' => Md5Password::make('123')])->save();

        return $u;
    }

    public function run(): void
    {
        // Akun Super Admin
        $this->user([
            'nama' => 'Super Admin', 
            'email' => 'admin@kantin.test', 
            'username' => 'admin'
        ], User::SUPER_ADMIN, null);

        $defs = [
            ['Toko Ifkar', 'Sembako', 'Ahmad Ifkar', 'ifkar@toko.id', 'ifkar', 'aktif'],
            ['Toko Koko', 'Kebutuhan Rumah', 'Koko Santoso', 'koko@toko.id', 'koko', 'aktif'],
            ['Toko Kucing Sehat', 'Pet Food', 'Nadia Putri', 'nadia@toko.id', 'nadia', 'aktif'],
            ['Warung Bu Sari', 'Sembako', 'Sari Wulandari', null, null, 'nonaktif'],
        ];
        $tenants = [];
        foreach ($defs as [$nama, $kat, $pj, $email, $uname, $status]) {
            $t = new Tenant(['nama_tenants' => $nama, 'kategori_usaha' => $kat, 'penanggung_jawab' => $pj, 'status' => $status]);
            $t->save();
            $tenants[$nama] = $t;
            if ($email) {
                $this->user(['nama' => $pj, 'email' => $email, 'username' => $uname], User::ADMIN_TENANT, $t->id);
            }
        }

        // ---- Toko Ifkar ----
        $ifkar = $tenants['Toko Ifkar'];
        $kasir = [
            $this->user(['nama' => 'Rani Puspita', 'username' => 'rani.kasir'], User::KASIR, $ifkar->id),
            $this->user(['nama' => 'Dedi Kurnia', 'username' => 'dedi.kasir'], User::KASIR, $ifkar->id),
            $this->user(['nama' => 'Siti Amelia', 'username' => 'siti.kasir', 'status' => 'nonaktif'], User::KASIR, $ifkar->id),
        ];

        $kategori = [];
        foreach (['Minuman', 'Makanan'] as $k) {
            $c = new Category(['nama_kategori' => $k]);
            $c->forceFill(['tenant_id' => $ifkar->id])->save();
            $kategori[$k] = $c->id;
        }

        $produk = [];
        foreach ([['Es Teh', 'Minuman', 5000, 48], ['Es Susu', 'Minuman', 12000, 30], ['Kopi Hitam', 'Minuman', 8000, 5], ['Roti Bakar', 'Makanan', 10000, 0]] as [$n, $k, $h, $s]) {
            $p = new Product(['nama_product' => $n, 'harga' => $h]);
            $p->forceFill(['tenant_id' => $ifkar->id, 'category_id' => $kategori[$k], 'stok' => $s, 'is_active' => true])->save();
            $produk[$n] = $p;
        }

        // Riwayat contoh
        mt_srand(42);
        $counter = 1000;
        for ($d = 6; $d >= 0; $d--) {
            $jumlahTrx = $d === 0 ? 4 : mt_rand(5, 9);
            for ($i = 0; $i < $jumlahTrx; $i++) {
                $waktu = now()->subDays($d)->setTime(mt_rand(8, 17), mt_rand(0, 59));
                if ($d === 0 && $waktu->isFuture()) {
                    $waktu = now()->subMinutes(30 * ($i + 1));
                }
                $items = [];
                
                // PERBAIKAN ERROR array_rand() ADA DI SINI
                $randomKeys = array_rand($produk, mt_rand(1, 3));
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                
                foreach ($randomKeys as $nama) {
                    $items[] = [$produk[$nama], mt_rand(1, 4)];
                }
                $total = array_sum(array_map(fn ($x) => $x[0]->harga * $x[1], $items));
                $counter++;

                $id = DB::table('transaksi')->insertGetId([
                    'tenant_id' => $ifkar->id, 'user_id' => $kasir[$i % 2]->id, 'no_transaksi' => 'TRX-' . $counter,
                    'total_bayar' => $total, 'metode_bayar' => $i % 3 === 0 ? 'transfer' : 'tunai',
                    'status' => ($d === 0 && $i === 3) ? 'dibatalkan' : 'selesai', 'created_at' => $waktu, 'updated_at' => $waktu,
                ]);
                foreach ($items as [$p, $q]) {
                    DB::table('transaksi_detail')->insert([
                        'transaksi_id' => $id, 'product_id' => $p->id, 'nama_product' => $p->nama_product,
                        'harga_satuan' => $p->harga, 'jumlah' => $q, 'subtotal' => $p->harga * $q,
                    ]);
                }
            }
        }
        $ifkar->forceFill(['trx_counter' => $counter])->save();
    }
}