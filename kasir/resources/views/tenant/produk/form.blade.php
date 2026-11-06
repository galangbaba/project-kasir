@extends('layouts.panel')
@use('App\Support\Format')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<form class="card form section" method="POST" action="{{ $product->exists ? route('tenant.produk.update', $product) : route('tenant.produk.store') }}" data-once>
    @csrf @if ($product->exists) @method('PUT') @endif
    <div class="field"><label for="nama_product">Nama Produk</label>
        <input class="input @error('nama_product') invalid @enderror" id="nama_product" name="nama_product" value="{{ old('nama_product', $product->nama_product) }}" maxlength="100" required>
        @error('nama_product')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="row2">
        <div class="field"><label for="kategori">Kategori</label>
            <input class="input" id="kategori" name="kategori" list="kat" value="{{ old('kategori', $product->category?->nama_kategori) }}" maxlength="60" placeholder="mis. Minuman">
            <datalist id="kat">@foreach ($kategori as $k)<option value="{{ $k }}">@endforeach</datalist>
            @error('kategori')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="harga">Harga (Rp)</label>
            <input class="input @error('harga') invalid @enderror" id="harga" name="harga" type="number" inputmode="numeric" min="1" max="100000000" step="1" value="{{ old('harga', $product->harga) }}" required>
            @error('harga')<div class="err">{{ $message }}</div>@enderror</div>
    </div>
    @unless ($product->exists)
    <div class="field"><label for="stok">Stok Awal</label>
        <input class="input @error('stok') invalid @enderror" id="stok" name="stok" type="number" inputmode="numeric" min="0" max="1000000" step="1" value="{{ old('stok', 0) }}" required>
        @error('stok')<div class="err">{{ $message }}</div>@enderror</div>
    @endunless
    <div class="acts"><button class="btn orange" type="submit">Simpan</button><a class="btn ghost" href="{{ route('tenant.produk') }}">Batal</a></div>
</form>

@if ($product->exists)
<form id="stok" class="card form section" method="POST" action="{{ route('tenant.produk.stok', $product) }}" data-once>
    @csrf
    <h2 class="h2">Penyesuaian Stok <span class="hint">(stok saat ini: {{ $product->stok }} pcs)</span></h2>
    <div class="row2">
        <div class="field"><label for="jenis">Jenis</label>
            <select class="input" id="jenis" name="jenis">
                <option value="restock" @selected(old('jenis') === 'restock')>Restock (tambah stok)</option>
                <option value="koreksi" @selected(old('jenis') === 'koreksi')>Koreksi (set stok jadi…)</option>
            </select></div>
        <div class="field"><label for="jumlah">Jumlah</label>
            <input class="input @error('jumlah') invalid @enderror" id="jumlah" name="jumlah" type="number" inputmode="numeric" min="0" max="1000000" value="{{ old('jumlah') }}" required>
            @error('jumlah')<div class="err">{{ $message }}</div>@enderror</div>
    </div>
    <div class="field"><label for="keterangan">Keterangan <span class="hint">(wajib untuk koreksi)</span></label>
        <input class="input @error('keterangan') invalid @enderror" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="200">
        @error('keterangan')<div class="err">{{ $message }}</div>@enderror</div>
    <button class="btn" type="submit">Simpan Penyesuaian</button>

    @if (isset($logs) && $logs->count())
        <h3 class="h3">Riwayat Stok Terakhir</h3>
        <div class="tbl-wrap"><table class="tbl">
            <thead><tr><th>Waktu</th><th>Jenis</th><th class="num">Perubahan</th><th class="num">Stok</th></tr></thead>
            <tbody>@foreach ($logs as $l)
                <tr><td>{{ \Illuminate\Support\Carbon::parse($l->created_at)->format('d/m H:i') }}</td><td>{{ ucfirst($l->tipe) }}</td>
                    <td class="num">{{ $l->perubahan > 0 ? '+' : '' }}{{ $l->perubahan }}</td><td class="num">{{ $l->stok_sesudah }}</td></tr>
            @endforeach</tbody></table></div>
    @endif
</form>

<form class="card form" method="POST" action="{{ route('tenant.produk.toggle', $product) }}" data-confirm="{{ $product->is_active ? 'Nonaktifkan produk ini? Produk tidak akan tampil di layar kasir.' : 'Aktifkan kembali produk ini?' }}">
    @csrf
    <h2 class="h2">Status Produk</h2>
    <p class="hint mt0">Produk yang pernah ditransaksikan tidak dihapus permanen, hanya dinonaktifkan.</p>
    <button class="btn {{ $product->is_active ? 'outline' : '' }}" type="submit">{{ $product->is_active ? 'Nonaktifkan Produk' : 'Aktifkan Produk' }}</button>
</form>
@endif
@endsection
