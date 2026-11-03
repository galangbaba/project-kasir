@extends('layouts.app')

@section('title', 'Data Kasir')

@section('content')
    <div class="container">
        <h1>Data Kasir</h1>

        <table border="1" cellpadding="10">
                <tr>
                    <th>ID</th>
                    <th>Nama Kasir</th>
                </tr>

                @forelse ($Kasirs as $kasir)
                    <tr>
                        <td>{{ $kasir->id }}</td>
                        <td>{{ $kasir->nama_kasir }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">Tidak ada data kasir.</td>
                    </tr>
                @endforelse
        </table>
    </div>
@endsection
