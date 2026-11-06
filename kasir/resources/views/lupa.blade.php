@extends('layouts.base')
@section('title', 'Lupa kata sandi')
@section('page')
<div class="auth-wrap"><div class="auth-card">
    <div class="logo">K</div>
    <h1>Lupa kata sandi?</h1>
    <p class="sub">Demi keamanan, kata sandi tidak dikirim lewat email. Hubungi atasan Anda untuk mengatur ulang:</p>
    <p class="lefty">
        • <b>Kasir</b> → hubungi Admin Tenant<br>
        • <b>Admin Tenant</b> → hubungi Super Admin<br>
        • <b>Super Admin</b> → hubungi pengelola server
    </p>
    <div class="foot"><a href="{{ route('portal') }}">← Kembali</a></div>
</div></div>
@endsection
