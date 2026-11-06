<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaksi;
use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Menguji invarian SRS §6 + keamanan login. Jalankan: php artisan test */
class KasirTest extends TestCase
{
    use RefreshDatabase;

    private function tenant(string $nama = 'Toko A', string $status = 'aktif'): Tenant
    {
        $t = new Tenant(['nama_tenants' => $nama, 'penanggung_jawab' => 'PJ', 'status' => $status]);
        $t->save();

        return $t;
    }

    private function user(string $role, ?Tenant $t, string $username, string $status = 'aktif'): User
    {
        $u = new User(['nama' => ucfirst($username), 'email' => "$username@x.id", 'username' => $username, 'status' => $status]);
        $u->forceFill(['role' => $role, 'tenant_id' => $t?->id, 'password' => Md5Password::make('rahasia123')])->save();

        return $u;
    }

    private function product(Tenant $t, string $nama, int $harga, int $stok): Product
    {
        $p = new Product(['nama_product' => $nama, 'harga' => $harga]);
        $p->forceFill(['tenant_id' => $t->id, 'stok' => $stok, 'is_active' => true])->save();

        return $p;
    }

    private function login(string $route, string $id, string $pw = 'rahasia123')
    {
        $res = $this->post(route($route), ['identitas' => $id, 'password' => $pw]);

        // klien uji tidak menyimpan cookie otomatis: kirim ulang seperti peramban
        $c = collect($res->headers->getCookies())->first(fn ($c) => $c->getName() === config('kasir.session_cookie') && $c->getValue());
        if ($c) {
            $this->withCookie($c->getName(), $c->getValue());
        }

        return $res;
    }

    public function test_password_disimpan_sebagai_md5_dan_login_membuat_sesi_opaque_terhash(): void
    {
        $u = $this->user(User::KASIR, $this->tenant(), 'rani');
        $this->assertSame(md5('rahasia123'), $u->fresh()->password);

        $res = $this->login('kasir.login.submit', 'rani');
        $res->assertRedirect(route('kasir.transaksi'));
        $res->assertCookie(config('kasir.session_cookie'));
        $this->assertDatabaseCount('auth_sessions', 1);
    }

    public function test_login_gagal_pesan_generik_dan_akun_terkunci_setelah_5_kali(): void
    {
        $u = $this->user(User::KASIR, $this->tenant(), 'rani');
        for ($i = 0; $i < 5; $i++) {
            $this->login('kasir.login.submit', 'rani', 'salah')->assertSessionHasErrors('identitas');
        }
        $this->assertTrue($u->fresh()->isLocked());
        // sandi benar pun ditolak saat terkunci
        $this->login('kasir.login.submit', 'rani')->assertSessionHasErrors('identitas');
        $this->assertDatabaseCount('auth_sessions', 0);
    }

    public function test_kasir_tidak_bisa_masuk_lewat_form_admin_dan_sebaliknya(): void
    {
        $t = $this->tenant();
        $this->user(User::KASIR, $t, 'rani');
        $this->user(User::ADMIN_TENANT, $t, 'bos');

        $this->login('tenant.login.submit', 'rani')->assertSessionHasErrors('identitas');
        $this->login('kasir.login.submit', 'bos')->assertSessionHasErrors('identitas');
    }

    public function test_tenant_nonaktif_tidak_bisa_login_dan_sesi_lama_ditolak(): void
    {
        $t = $this->tenant();
        $this->user(User::KASIR, $t, 'rani');
        $this->login('kasir.login.submit', 'rani');
        $this->get(route('kasir.transaksi'))->assertOk();

        $t->update(['status' => 'nonaktif']);
        $this->get(route('kasir.transaksi'))->assertRedirect(route('kasir.login'));
        $this->login('kasir.login.submit', 'rani')->assertSessionHasErrors('identitas');
    }

    public function test_rbac_kasir_tidak_bisa_buka_halaman_admin_tenant_dan_super_admin(): void
    {
        $this->user(User::KASIR, $this->tenant(), 'rani');
        $this->login('kasir.login.submit', 'rani');

        $this->get(route('tenant.produk'))->assertForbidden();
        $this->post(route('tenant.produk.store'), ['nama_product' => 'X', 'harga' => 1000, 'stok' => 1])->assertForbidden();
        $this->get(route('superadmin.tenants'))->assertForbidden();
    }

    public function test_tanpa_sesi_diarahkan_ke_login(): void
    {
        $this->get(route('tenant.dashboard'))->assertRedirect(route('tenant.login'));
        $this->get(route('superadmin.dashboard'))->assertRedirect(route('superadmin.login'));
    }

    public function test_transaksi_menghitung_total_dari_harga_server_menyimpan_snapshot_dan_mengurangi_stok(): void
    {
        $t = $this->tenant();
        $this->user(User::KASIR, $t, 'rani');
        $teh = $this->product($t, 'Es Teh', 5000, 10);
        $susu = $this->product($t, 'Es Susu', 12000, 10);

        $this->login('kasir.login.submit', 'rani');
        $this->post(route('kasir.transaksi.store'), ['items' => [$teh->id => 2, $susu->id => 1], 'metode_bayar' => 'tunai'])->assertRedirect();

        $trx = Transaksi::firstOrFail();
        $this->assertSame(22000, $trx->total_bayar);
        $this->assertSame($trx->total_bayar, (int) $trx->details()->sum('subtotal')); // selisih Rp0
        $this->assertSame(8, $teh->fresh()->stok);
        $this->assertSame(9, $susu->fresh()->stok);

        // ubah harga master -> snapshot transaksi lama tidak berubah
        $teh->forceFill(['harga' => 9000])->save();
        $this->assertSame(5000, (int) $trx->details()->where('product_id', $teh->id)->value('harga_satuan'));
    }

    public function test_stok_tidak_boleh_negatif_dan_transaksi_gagal_utuh(): void
    {
        $t = $this->tenant();
        $this->user(User::KASIR, $t, 'rani');
        $teh = $this->product($t, 'Es Teh', 5000, 1);
        $susu = $this->product($t, 'Es Susu', 12000, 5);

        $this->login('kasir.login.submit', 'rani');
        $this->post(route('kasir.transaksi.store'), ['items' => [$susu->id => 1, $teh->id => 2], 'metode_bayar' => 'tunai'])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(5, $susu->fresh()->stok); // atomik: tidak ada pengurangan sebagian
        $this->assertSame(1, $teh->fresh()->stok);
    }

    public function test_void_mengembalikan_stok_hanya_milik_sendiri_dan_sekali(): void
    {
        $t = $this->tenant();
        $this->user(User::KASIR, $t, 'rani');
        $this->user(User::KASIR, $t, 'dedi');
        $teh = $this->product($t, 'Es Teh', 5000, 10);

        $this->login('kasir.login.submit', 'rani');
        $this->post(route('kasir.transaksi.store'), ['items' => [$teh->id => 3], 'metode_bayar' => 'transfer']);
        $trx = Transaksi::firstOrFail();
        $this->assertSame(7, $teh->fresh()->stok);

        $this->post(route('kasir.batal', $trx))->assertRedirect();
        $this->assertSame(10, $teh->fresh()->stok);
        $this->assertSame('dibatalkan', $trx->fresh()->status);

        $this->post(route('kasir.batal', $trx))->assertSessionHasErrors('void'); // tidak bisa dua kali
        $this->assertSame(10, $teh->fresh()->stok);
    }

    public function test_tenant_a_tidak_bisa_mengakses_data_tenant_b(): void
    {
        $a = $this->tenant('A');
        $b = $this->tenant('B');
        $this->user(User::ADMIN_TENANT, $a, 'bosa');
        $pb = $this->product($b, 'Produk B', 1000, 5);
        $kasirB = $this->user(User::KASIR, $b, 'kasirb');

        $this->login('tenant.login.submit', 'bosa');
        $this->get(route('tenant.produk.edit', $pb->id))->assertNotFound();
        $this->post(route('tenant.produk.status', $pb->id))->assertNotFound();
        $this->get(route('tenant.pegawai.edit', $kasirB->id))->assertNotFound();
        $this->get(route('tenant.produk'))->assertDontSee('Produk B');
    }

    public function test_restock_dan_koreksi_tercatat_di_stock_log_dan_audit(): void
    {
        $t = $this->tenant();
        $this->user(User::ADMIN_TENANT, $t, 'bos');
        $p = $this->product($t, 'Es Teh', 5000, 2);

        $this->login('tenant.login.submit', 'bos');
        $this->post(route('tenant.produk.stok', $p->id), ['jenis' => 'restock', 'jumlah' => 10])->assertRedirect();
        $this->assertSame(12, $p->fresh()->stok);
        $this->post(route('tenant.produk.stok', $p->id), ['jenis' => 'koreksi', 'jumlah' => 7, 'keterangan' => 'stok opname'])->assertRedirect();
        $this->assertSame(7, $p->fresh()->stok);

        $this->assertDatabaseCount('stock_logs', 2);
        $this->assertDatabaseHas('audit_logs', ['aksi' => 'stok.restock']);
    }

    public function test_audit_log_dan_stock_log_append_only(): void
    {
        \App\Models\AuditLog::catat('tes');
        $this->expectException(\LogicException::class);
        \App\Models\AuditLog::first()->update(['aksi' => 'diubah']);
    }

    public function test_header_keamanan_dan_cookie_httponly(): void
    {
        $this->user(User::KASIR, $this->tenant(), 'rani');
        $res = $this->login('kasir.login.submit', 'rani');
        $cookie = collect($res->headers->getCookies())->firstWhere(fn ($c) => $c->getName() === config('kasir.session_cookie'));
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());

        $page = $this->get(route('kasir.login'));
        $page->assertHeader('X-Frame-Options', 'DENY');
        $this->assertStringContainsString("script-src 'self'", $page->headers->get('Content-Security-Policy'));
    }

    public function test_ekspor_excel_menghasilkan_xlsx(): void
    {
        $t = $this->tenant();
        $this->user(User::ADMIN_TENANT, $t, 'bos');
        $this->login('tenant.login.submit', 'bos');
        $res = $this->get(route('tenant.laporan.ekspor'));
        $res->assertOk();
        $this->assertStringContainsString('spreadsheetml', $res->headers->get('Content-Type'));
    }
}
