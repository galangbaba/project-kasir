@extends('layouts.base')
@section('title', 'Masuk')
@section('page')
<div class="auth-wrap"><div class="auth-card">
    <div class="logo">K</div>
    <h1>KasirKu</h1>
    <p class="sub">Aplikasi kasir multi-client. Pilih peran Anda.</p>
    <div class="portal">
        <a href="{{ route('superadmin.login') }}">Super Admin <small>Kelola seluruh client/tenant</small></a>
        <a href="{{ route('tenant.login') }}">Admin Tenant <small>Produk, stok, pegawai, laporan</small></a>
        <a href="{{ route('kasir.login') }}">Pegawai / Kasir <small>Transaksi penjualan</small></a>
    </div>
</div></div>
@endsection
