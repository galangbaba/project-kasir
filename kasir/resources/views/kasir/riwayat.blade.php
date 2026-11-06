@extends('layouts.kasir')
@use('App\Support\Format')
@section('title', 'Riwayat Transaksi')
@section('content')
<div class="today">
    <div class="l">Transaksi Hari Ini</div>
    <div class="r"><b>{{ $jumlah }} transaksi</b><b class="m">{{ Format::rupiah($total) }}</b></div>
</div>

@forelse ($daftar as $t)
    <a class="trx" href="{{ route('kasir.struk', $t) }}">
        <div><b>#{{ $t->no_transaksi }}</b><div class="t">{{ $t->created_at->format('H:i') }} · {{ ucfirst($t->metode_bayar) }}</div></div>
        <div class="rt"><b>{{ Format::rupiah($t->total_bayar) }}</b><div><span class="badge {{ $t->isSelesai() ? 'b-green' : 'b-red' }}">{{ $t->isSelesai() ? 'Selesai' : 'Dibatalkan' }}</span></div></div>
    </a>
@empty
    <div class="empty">Belum ada transaksi hari ini.</div>
@endforelse
@endsection
