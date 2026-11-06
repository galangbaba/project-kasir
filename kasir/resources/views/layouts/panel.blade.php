@extends('layouts.base')
@php
    $u = auth()->user();
    $isSuper = $u->role === 'super_admin';
    $menu = $isSuper
        ? [['superadmin.dashboard', 'Dashboard', 'superadmin.dashboard'], ['superadmin.tenants', 'Kelola Tenant', 'superadmin.tenants*'],
           ['superadmin.admin', 'Kelola Admin Tenant', 'superadmin.admin*'], ['superadmin.pengaturan', 'Pengaturan', 'superadmin.pengaturan*']]
        : [['tenant.dashboard', 'Dashboard', 'tenant.dashboard'], ['tenant.produk', 'Produk & Stok', 'tenant.produk*'],
           ['tenant.pegawai', 'Pegawai / Kasir', 'tenant.pegawai*'], ['tenant.laporan', 'Laporan Penjualan', 'tenant.laporan*']];
    $brand = $isSuper ? 'KasirKu' : $u->tenant->nama_tenants;
@endphp
@section('page')
<aside class="sidebar" id="sidebar" aria-label="Menu utama">
    <div class="brand"><div class="mark">{{ mb_strtoupper(mb_substr($brand, 0, 1)) }}</div><span class="t">{{ $brand }}</span></div>
    <nav class="nav">
        @foreach ($menu as [$route, $label, $pattern])
            <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'on' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="side-foot">
        <div class="who">{{ $u->nama }}</div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
    </div>
</aside>
<div class="scrim" data-toggle-nav></div>

<div class="main">
    <header class="topbar">
        <button class="burger" type="button" data-toggle-nav aria-label="Buka menu">☰</button>
        <h1>@yield('title')@hasSection('periode')<span class="periode">@yield('periode')</span>@endif</h1>
        @hasSection('actions') @yield('actions') @else <span class="pill">{{ $isSuper ? 'Super Admin' : 'Admin Tenant' }}</span> @endif
    </header>
    <main class="content">
        @include('partials.flash')
        @yield('content')
    </main>
</div>
@endsection
