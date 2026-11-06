@extends('layouts.base')
@section('title', $title)
@section('page')
<div class="auth-wrap"><div class="auth-card">
    <div class="logo">K</div>
    <h1>{{ $title }}</h1>
    <p class="sub">{{ $subtitle }}</p>

    <form method="POST" action="{{ route($role === 'super_admin' ? 'superadmin.login.submit' : ($role === 'admin_tenant' ? 'tenant.login.submit' : 'kasir.login.submit')) }}" data-once autocomplete="on">
        @csrf
        <div class="field">
            <label for="identitas">{{ $label }}</label>
            <input id="identitas" name="identitas" class="input @error('identitas') invalid @enderror" value="{{ old('identitas') }}"
                   placeholder="{{ $placeholder }}" autocomplete="username" autocapitalize="none" autocorrect="off" required autofocus maxlength="150">
            @error('identitas')<div class="err" role="alert">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label for="password">Kata Sandi</label>
            <input id="password" name="password" type="password" class="input" autocomplete="current-password" placeholder="••••••••" required maxlength="200">
        </div>
        <button class="btn block" type="submit">Masuk</button>
    </form>

    <div class="foot">
        @if ($role === 'kasir') Lupa kata sandi? Hubungi Admin Tenant
        @else <a href="{{ route('lupa') }}">Lupa kata sandi?</a> @endif
    </div>
</div></div>
@endsection
