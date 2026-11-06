@extends('layouts.panel')
@section('title', 'Pengaturan')
@section('content')
<div class="card form section">
    <h2 class="h2">Akun</h2>
    <p class="m0">{{ auth()->user()->nama }}<br><span class="hint">{{ auth()->user()->email }} · {{ auth()->user()->username }}</span></p>
</div>
<form class="card form" method="POST" action="{{ route('superadmin.pengaturan.sandi') }}" data-once autocomplete="off">
    @csrf
    <h2 class="h2">Ganti Kata Sandi</h2>
    <div class="field"><label for="password_lama">Kata sandi lama</label>
        <input class="input @error('password_lama') invalid @enderror" id="password_lama" name="password_lama" type="password" autocomplete="current-password" required>
        @error('password_lama')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="password">Kata sandi baru</label>
        <input class="input @error('password') invalid @enderror" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
        @error('password')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="password_confirmation">Ulangi kata sandi baru</label>
        <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
    <button class="btn orange" type="submit">Simpan</button>
    <p class="hint">Setelah diganti, perangkat lain yang sedang login akan keluar otomatis.</p>
</form>
@endsection
