@extends('layouts.panel')
@section('title', 'Kelola Akun Admin Tenant')
@section('actions')<a class="btn orange" href="{{ route('superadmin.admin.create') }}">+ Tambah Admin</a>@endsection
@section('content')
<div class="tbl-wrap">
    <table class="tbl stack">
        <thead><tr><th>Nama</th><th>Tenant</th><th>Email</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($admins as $a)
            <tr>
                <td data-label="Nama">{{ $a->nama }}</td>
                <td data-label="Tenant">{{ $a->tenant?->nama_tenants }}</td>
                <td data-label="Email">{{ $a->email }}</td>
                <td data-label="Status"><span class="badge {{ $a->isActive() ? 'b-green' : 'b-red' }}">{{ $a->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td data-label="Aksi" class="noLabel"><div class="acts"><a href="{{ route('superadmin.admin.edit', $a) }}"><b>Edit</b></a></div></td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Belum ada akun Admin Tenant.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $admins->links() }}
@endsection
