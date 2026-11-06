@extends('layouts.kasir')
@use('App\Support\Format')
@section('title', 'Struk')
@section('content')
@php $batal = ! $trx->isSelesai(); @endphp
<div class="struk-ok {{ $batal ? 'bad' : '' }}">
    <div class="ck">{{ $batal ? '✕' : '✓' }}</div>
    <h2>{{ $batal ? 'Transaksi Dibatalkan' : 'Transaksi Berhasil' }}</h2>
</div>

<div class="struk">
    <div class="nm">{{ $trx->tenant->nama_tenants }}</div>
    <div class="meta">No. {{ $trx->no_transaksi }} • {{ $trx->created_at->copy()->locale('id')->translatedFormat('j M Y, H:i') }}</div>
    <hr>
    @foreach ($trx->details as $d)
        <div class="r"><span>{{ $d->nama_product }} x{{ $d->jumlah }}</span><span>{{ Format::rupiah($d->subtotal) }}</span></div>
    @endforeach
    <hr>
    <div class="r t"><span>Total</span><b>{{ Format::rupiah($trx->total_bayar) }}</b></div>
    <div class="r mut"><span>Metode</span><b>{{ ucfirst($trx->metode_bayar) }}</b></div>
    <div class="r mut"><span>Kasir</span><b>{{ $trx->kasir->nama }}</b></div>
    @if ($batal)<div class="r mut"><span>Status</span><b>Dibatalkan</b></div>@endif
</div>

<div class="stack-btns">
    <button type="button" class="btn outline" data-print>Cetak Struk</button>
    <a class="btn orange" href="{{ route('kasir.transaksi') }}">Transaksi Baru</a>
</div>

@if ($trx->bisaDibatalkan())
    <form class="noprint" method="POST" action="{{ route('kasir.batal', $trx) }}" data-confirm="Batalkan transaksi {{ $trx->no_transaksi }}? Stok akan dikembalikan." data-once>
        @csrf
        <button class="btn ghost block act-red" type="submit">Batalkan Transaksi (void)</button>
        <p class="hint">Bisa dibatalkan sampai {{ config('kasir.void_minutes') }} menit setelah transaksi dibuat.</p>
    </form>
@endif
@endsection
