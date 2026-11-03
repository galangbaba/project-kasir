@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard Kasir</h1>
    <p>Selamat datang di aplikasi kasir.</p>

    <p>Jumlah kasir: {{ $jumlahKasir }}</p>
    <p>Jumlah menu minuman: {{ $jumlahMenu }}</p>
    <p>Jumlah transaksi: {{ $jumlahTransaksi }}</p>
@endsection
