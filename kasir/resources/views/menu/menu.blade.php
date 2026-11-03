@extends('layouts.app')

@section('title', 'Menu Minuman')

@section('content')
    <div class="container">
        <h1>Menu Minuman</h1>

        <table border="1" cellpadding="10">
            <tr>
                <th>Nama Minuman</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>

            @forelse ($menus as $menu)
                <tr>
                    <td>{{ $menu->nama_minuman }}</td>
                    <td>Rp. {{ number_format($menu->harga, 0, ',', '.') }}</td>
                    <td>{{ $menu->stok }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Tidak ada data menu minuman.</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection
