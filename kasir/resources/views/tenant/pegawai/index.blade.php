@extends('layouts.panel')
@section('title', 'Kelola Pegawai / Kasir')
@section('actions')<a class="btn orange" href="{{ route('tenant.pegawai.create') }}">+ Tambah Pegawai</a>@endsection
@section('content')
<div class="tbl-wrap">
    <table class="tbl stack">
        <thead><tr><th>Nama Pegawai</th><th>Username</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($pegawai as $p)
            <tr>
                <td data-label="Nama Pegawai">{{ $p->nama }}</td>
                <td data-label="Username">{{ $p->username }}</td>
                <td data-label="Status"><span class="badge {{ $p->isActive() ? 'b-green' : 'b-red' }}">{{ $p->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td data-label="Aksi" class="noLabel"><div class="acts">
                    <a href="{{ route('tenant.pegawai.edit', $p->id) }}">Edit</a>
                    <form method="POST" action="{{ route('tenant.pegawai.toggle', $p->id) }}" data-confirm="{{ $p->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $p->nama }}?">
                        @csrf
                        <button class="linkbtn {{ $p->isActive() ? 'act-red' : 'act-green' }}" type="submit">{{ $p->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada akun kasir.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $pegawai->links() }}
@endsection
