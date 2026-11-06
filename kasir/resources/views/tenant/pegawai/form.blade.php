@extends('layouts.panel')
@section('title', $pegawai->exists ? 'Edit Pegawai' : 'Tambah Pegawai')
@section('content')
<form class="card form" method="POST" action="{{ $pegawai->exists ? route('tenant.pegawai.update', $pegawai->id) : route('tenant.pegawai.store') }}" data-once autocomplete="off">
    @csrf @if ($pegawai->exists) @method('PUT') @endif
    <div class="field"><label for="nama">Nama Pegawai</label>
        <input class="input @error('nama') invalid @enderror" id="nama" name="nama" value="{{ old('nama', $pegawai->nama) }}" maxlength="100" required>
        @error('nama')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="username">Username</label>
        <input class="input @error('username') invalid @enderror" id="username" name="username" value="{{ old('username', $pegawai->username) }}" maxlength="50" required autocapitalize="none" placeholder="mis. rani.kasir">
        @error('username')<div class="err">{{ $message }}</div>@enderror
        <div class="hint">Huruf kecil, angka, titik, strip (3–50 karakter).</div></div>
    <div class="field"><label for="password">Kata Sandi {{ $pegawai->exists ? '(kosongkan bila tidak diganti)' : '' }}</label>
        <input class="input @error('password') invalid @enderror" id="password" name="password" type="password" minlength="8" maxlength="100" autocomplete="new-password" {{ $pegawai->exists ? '' : 'required' }}>
        @error('password')<div class="err">{{ $message }}</div>@enderror
        <div class="hint">Minimal 8 karakter.</div></div>
    <div class="acts"><button class="btn orange" type="submit">Simpan</button><a class="btn ghost" href="{{ route('tenant.pegawai') }}">Batal</a></div>
</form>
@endsection
