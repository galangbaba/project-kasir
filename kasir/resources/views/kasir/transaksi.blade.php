@extends('layouts.kasir')
@use('App\Support\Format')
@section('title', 'Transaksi Baru')
@section('back')<button type="button" id="btnBack" class="linkbtn back" aria-label="Kembali" hidden>‹</button>@endsection
@section('content')
<form id="pos" method="POST" action="{{ route('kasir.transaksi.store') }}">
    @csrf
    <div id="posSub" class="k-sub">{{ auth()->user()->tenant->nama_tenants }} • {{ auth()->user()->nama }}</div>

    <div id="stepProducts">
        <div class="plist">
            @forelse ($products as $p)
                <div class="prod {{ $p->stok <= 0 ? 'out' : '' }}" data-id="{{ $p->id }}" data-harga="{{ $p->harga }}" data-stok="{{ $p->stok }}" data-nama="{{ $p->nama_product }}">
                    <div class="ico c{{ ($p->id % 6) + 1 }}">{{ mb_strtoupper(mb_substr($p->nama_product, 0, 1)) }}</div>
                    <div class="info">
                        <div class="nm">{{ $p->nama_product }}</div>
                        <div class="hg">{{ Format::rupiah($p->harga) }}</div>
                        <div class="sisa">{{ $p->stok > 0 ? 'Stok ' . $p->stok : 'Habis' }}</div>
                    </div>
                    <div class="qty">
                        <button type="button" data-act="dec" aria-label="Kurangi {{ $p->nama_product }}" disabled>−</button>
                        <span class="q">0</span>
                        <button type="button" data-act="inc" aria-label="Tambah {{ $p->nama_product }}" {{ $p->stok <= 0 ? 'disabled' : '' }}>+</button>
                    </div>
                </div>
            @empty
                <div class="empty">Belum ada produk aktif. Hubungi Admin Tenant.</div>
            @endforelse
        </div>
    </div>

    <div id="stepPay" hidden>
        <h2 class="h2">Ringkasan Pesanan</h2>
        <div class="sum">
            <div id="sumLines"></div>
            <hr>
            <div class="r total"><span>Total Pembayaran</span><b data-total>Rp0</b></div>
        </div>
        <h3 class="h3">Metode Pembayaran</h3>
        <div class="methods">
            <label><input type="radio" name="metode_bayar" value="tunai" checked><span>Tunai</span></label>
            <label><input type="radio" name="metode_bayar" value="transfer"><span>Transfer</span></label>
        </div>
    </div>

    <div class="k-foot"><div class="in">
        <div class="tot"><small>Total (<span data-count>0</span> item)</small><b data-total>Rp0</b></div>
        <button type="button" id="btnBayar" class="btn orange" disabled>Bayar</button>
        <button type="submit" id="btnDone" class="btn orange" hidden>Selesaikan Transaksi</button>
    </div></div>
</form>
@endsection
