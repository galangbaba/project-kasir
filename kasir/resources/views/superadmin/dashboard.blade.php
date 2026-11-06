@extends('layouts.panel')
@use('App\Support\Format')
@section('title', 'Dashboard Monitoring')
@section('content')
<div class="stats">
    <div class="stat"><div class="l">Tenant Aktif</div><div class="v c-blue">{{ $aktif }}</div><div class="s">{{ $baruBulanIni }} baru bulan ini</div></div>
    <div class="stat"><div class="l">Pendapatan Hari Ini</div><div class="v c-green">{{ Format::rupiahSingkat($omzet) }}</div><div class="s">seluruh tenant</div></div>
    <div class="stat"><div class="l">Total Transaksi</div><div class="v">{{ $jumlahTrx }}</div><div class="s">hari ini</div></div>
    <div class="stat"><div class="l">Tenant Nonaktif</div><div class="v c-red">{{ $nonaktif }}</div><div class="s">perlu ditinjau</div></div>
</div>

<h2 class="h2">Ringkasan per Tenant</h2>
<div class="tbl-wrap">
    <table class="tbl stack">
        <thead><tr><th>Nama Tenant</th><th>Kategori</th><th>Status</th><th>Pendapatan Hari Ini</th><th>Transaksi</th></tr></thead>
        <tbody>
        @forelse ($tenants as $t)
            @php $p = $perTenant->get($t->id); @endphp
            <tr>
                <td data-label="Nama Tenant">{{ $t->nama_tenants }}</td>
                <td data-label="Kategori">{{ $t->kategori_usaha ?: '-' }}</td>
                <td data-label="Status"><span class="badge {{ $t->isActive() ? 'b-green' : 'b-red' }}">{{ $t->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td data-label="Pendapatan Hari Ini">{{ Format::rupiah($p->omzet ?? 0) }}</td>
                <td data-label="Transaksi">{{ $p->jml ?? 0 }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Belum ada tenant.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
