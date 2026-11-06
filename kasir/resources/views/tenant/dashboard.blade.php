@extends('layouts.panel')
@use('App\Support\Format')
@section('title', 'Dashboard ' . auth()->user()->tenant->nama_tenants)
@section('content')
<div class="stats">
    <div class="stat"><div class="l">Pendapatan Hari Ini</div><div class="v c-green">{{ Format::rupiah($omzet) }}</div><div class="s">{{ $jumlahTrx }} transaksi</div></div>
    <div class="stat"><div class="l">Transaksi Hari Ini</div><div class="v">{{ $jumlahTrx }}</div><div class="s">{{ $kasirBertugas }} kasir bertugas</div></div>
    <div class="stat"><div class="l">Produk Aktif</div><div class="v c-blue">{{ $produkAktif }}</div><div class="s">dari {{ $produkTotal }} produk</div></div>
    <div class="stat"><div class="l">Stok Menipis</div><div class="v c-amber">{{ $stokMenipis }}</div><div class="s">perlu direstock</div></div>
</div>

<div class="grid2">
    <section>
        <h2 class="h2">Pendapatan 7 Hari Terakhir</h2>
        <div class="card">
            @include('partials.bars', ['items' => $harian->map(fn ($h) => ['omzet' => $h['omzet'], 'label' => $h['tanggal']->copy()->locale('id')->isoFormat('ddd')])])
        </div>
    </section>
    <section>
        <h2 class="h2">Produk Terlaris Hari Ini</h2>
        <div class="tbl-wrap">
            <table class="tbl">
                <thead><tr><th>Produk</th><th>Terjual</th><th>Omzet</th></tr></thead>
                <tbody>
                @forelse ($terlaris as $p)
                    <tr><td>{{ $p->nama }}</td><td>{{ (int) $p->qty }} pcs</td><td>{{ Format::rupiah($p->omzet) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty">Belum ada penjualan hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
