@extends('layouts.panel')
@use('App\Support\Format')
@section('title', 'Laporan Penjualan')
@section('periode', 'Periode: ' . Format::periode($from, $to))
@section('content')
<form class="toolbar" method="GET" action="{{ route('tenant.laporan') }}">
    <div class="field"><label for="dari">Dari</label><input class="input" type="date" id="dari" name="dari" value="{{ $from->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"></div>
    <div class="field"><label for="sampai">Sampai</label><input class="input" type="date" id="sampai" name="sampai" value="{{ $to->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"></div>
    <button class="btn" type="submit">Terapkan</button>
    <a class="btn outline" href="{{ route('tenant.laporan.ekspor', ['dari' => $from->format('Y-m-d'), 'sampai' => $to->format('Y-m-d')]) }}">Ekspor Excel</a>
</form>
@error('dari')<div class="alert bad">{{ $message }}</div>@enderror
@error('sampai')<div class="alert bad">{{ $message }}</div>@enderror

<div class="stats">
    <div class="stat"><div class="l">Total Pendapatan</div><div class="v c-green">{{ Format::rupiah($ringkas['omzet']) }}</div></div>
    <div class="stat"><div class="l">Jumlah Transaksi</div><div class="v">{{ $ringkas['jumlah'] }}</div></div>
    <div class="stat"><div class="l">Rata-rata / Transaksi</div><div class="v c-blue">{{ Format::rupiah($ringkas['rata']) }}</div></div>
</div>

<section class="section">
    <h2 class="h2">Tren Pendapatan Harian</h2>
    <div class="card">
        @include('partials.bars', ['items' => $harian->map(fn ($h) => ['omzet' => $h['omzet'], 'label' => $h['tanggal']->format('j')])])
    </div>
</section>

<section class="section">
    <h2 class="h2">Transaksi Terbaru</h2>
    <div class="tbl-wrap"><table class="tbl stack">
        <thead><tr><th>No. Transaksi</th><th>Kasir</th><th>Total</th><th>Waktu</th><th>Status</th></tr></thead>
        <tbody>
        @forelse ($terbaru as $t)
            <tr>
                <td data-label="No. Transaksi">#{{ $t->no_transaksi }}</td><td data-label="Kasir">{{ $t->kasir?->nama }}</td>
                <td data-label="Total">{{ Format::rupiah($t->total_bayar) }}</td>
                <td data-label="Waktu">{{ $t->created_at->format('H:i') }}{{ $t->created_at->isToday() ? '' : ' · ' . $t->created_at->format('d/m') }}</td>
                <td data-label="Status"><span class="badge {{ $t->isSelesai() ? 'b-green' : 'b-red' }}">{{ $t->isSelesai() ? 'Selesai' : 'Dibatalkan' }}</span></td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Tidak ada transaksi pada periode ini.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>

<div class="grid2 eq">
    <section>
        <h2 class="h2">Per Produk</h2>
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>Produk</th><th class="num">Qty</th><th class="num">Omzet</th></tr></thead>
            <tbody>@forelse ($perProduk as $p)<tr><td>{{ $p->nama }}</td><td class="num">{{ (int) $p->qty }}</td><td class="num">{{ Format::rupiah($p->omzet) }}</td></tr>
            @empty<tr><td colspan="3" class="empty">-</td></tr>@endforelse</tbody>
        </table></div>
    </section>
    <section>
        <h2 class="h2">Per Kasir</h2>
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>Kasir</th><th class="num">Transaksi</th><th class="num">Omzet</th></tr></thead>
            <tbody>@forelse ($perKasir as $p)<tr><td>{{ $p->nama }}</td><td class="num">{{ (int) $p->jml }}</td><td class="num">{{ Format::rupiah($p->omzet) }}</td></tr>
            @empty<tr><td colspan="3" class="empty">-</td></tr>@endforelse</tbody>
        </table></div>
    </section>
</div>
@endsection
