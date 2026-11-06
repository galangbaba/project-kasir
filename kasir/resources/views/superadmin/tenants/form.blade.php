@extends('layouts.panel')
@section('title', $tenant->exists ? 'Edit Tenant' : 'Tambah Tenant')
@section('content')
<form class="card form" method="POST" action="{{ $tenant->exists ? route('superadmin.tenants.update', $tenant) : route('superadmin.tenants.store') }}" data-once>
    @csrf @if ($tenant->exists) @method('PUT') @endif
    <div class="field"><label for="nama_tenants">Nama Tenant</label>
        <input class="input @error('nama_tenants') invalid @enderror" id="nama_tenants" name="nama_tenants" value="{{ old('nama_tenants', $tenant->nama_tenants) }}" maxlength="100" required>
        @error('nama_tenants')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="kategori_usaha">Kategori Usaha</label>
        <input class="input" id="kategori_usaha" name="kategori_usaha" value="{{ old('kategori_usaha', $tenant->kategori_usaha) }}" maxlength="60" placeholder="mis. Sembako">
        @error('kategori_usaha')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="penanggung_jawab">Penanggung Jawab</label>
        <input class="input @error('penanggung_jawab') invalid @enderror" id="penanggung_jawab" name="penanggung_jawab" value="{{ old('penanggung_jawab', $tenant->penanggung_jawab) }}" maxlength="100" required>
        @error('penanggung_jawab')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="acts"><button class="btn orange" type="submit">Simpan</button><a class="btn ghost" href="{{ route('superadmin.tenants') }}">Batal</a></div>
</form>
@endsection
