
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-heading">
    <h1>Dashboard</h1>
    <p>Kelola penjualan dan menu minuman dengan mudah.</p>
</div>

<div class="cards">
    <div class="card dark">
        <div class="card-label">Total Kasir</div>
        <h2>{{ $jumlahKasir }}</h2>
        <span class="accent">Kasir terdaftar</span>
    </div>

    <div class="card">
        <div class="card-label">Menu Minuman</div>
        <h2>{{ $jumlahMenu }}</h2>
        <span>Menu tersedia</span>
    </div>

    <div class="card">
        <div class="card-label">Total Transaksi</div>
        <h2>{{ $jumlahTransaksi }}</h2>
        <span>Transaksi tercatat</span>
    </div>

    <div class="card">
        <div class="card-label">Pendapatan</div>
        <h2>Rp {{ number_format($pendapatan, 0, ',', '.') }}</h2>
        <span>Total penjualan</span>
    </div>
</div>

<div class="section-grid">
    <div class="card">
        <div class="section-title">Statistik Penjualan</div>
        <p style="color:#829188">
            Grafik penjualan akan ditampilkan setelah data transaksi tersedia.
        </p>
    </div>

    <div class="card dark">
        <div class="section-title">Informasi Aplikasi</div>
        <h2 class="accent">KasirKu</h2>
        <p>Aplikasi pengelolaan kasir dan menu minuman.</p>
        <br>
        <a href="{{ route('menu.menu') }}" class="accent">
            Lihat Menu Minuman →
        </a>
    </div>
</div>

<div class="table-card">
    <div class="section-title">Menu Minuman Terbaru</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Minuman</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($menus as $index => $menu)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $menu->nama_menu }}</td>
                    <td>{{ $menu->kategori }}</td>
                    <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                    <td>{{ $menu->stok }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada menu minuman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
