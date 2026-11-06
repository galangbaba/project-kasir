@extends('layouts.base')
@section('body', 'kasir')
@section('page')
<header class="k-head"><div class="in">
    @yield('back')
    <h1 id="posTitle">@yield('title')</h1>
    <div class="links">
        <a href="{{ route(request()->routeIs('kasir.riwayat') ? 'kasir.transaksi' : 'kasir.riwayat') }}">{{ request()->routeIs('kasir.riwayat') ? 'Transaksi' : 'Riwayat' }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="linkbtn" type="submit">Keluar</button></form>
    </div>
</div></header>
<main class="k-wrap">
    @include('partials.flash')
    @yield('content')
</main>
@yield('foot')
@endsection
