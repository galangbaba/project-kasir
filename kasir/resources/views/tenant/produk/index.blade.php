@extends('layouts.panel')
@use('App\Support\Format')
@section('title', 'Produk & Stok')
@section('actions')<a class="btn orange" href="{{ route('tenant.produk.create') }}">+ Tambah Produk</a>@endsection
@section('content')
<div class="tbl-wrap">
    <table class="tbl stack">
        <thead><tr><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($products as $p)
            @php $st = $p->statusStok(); @endphp
            <tr>
                <td data-label="Nama Produk">{{ $p->nama_product }}</td>
                <td data-label="Kategori">{{ $p->category?->nama_kategori ?? '-' }}</td>
                <td data-label="Harga">{{ Format::rupiah($p->harga) }}</td>
                <td data-label="Stok">{{ $p->stok }} pcs</td>
                <td data-label="Status">
                    @if (! $p->is_active) <span class="badge b-gray">Nonaktif</span>
                    @elseif ($st === 'habis') <span class="badge b-red">Habis</span>
                    @elseif ($st === 'menipis') <span class="badge b-amber">Stok Menipis</span>
                    @else <span class="badge b-green">Tersedia</span> @endif
                </td>
                <td data-label="Aksi" class="noLabel"><div class="acts">
                    <a href="{{ route('tenant.produk.edit', $p) }}"><b>Edit</b></a>
                    @if ($p->is_active && $st !== 'tersedia') <a class="act-orange" href="{{ route('tenant.produk.edit', $p) }}#stok"><b>Restock</b></a> @endif
                </div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada produk. Klik “+ Tambah Produk”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $products->links() }}
@endsection
