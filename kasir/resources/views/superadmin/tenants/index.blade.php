@extends('layouts.panel')
@section('title', 'Kelola Client / Tenant')
@section('actions')<a class="btn orange" href="{{ route('superadmin.tenants.create') }}">+ Tambah Tenant</a>@endsection
@section('content')
<div class="tbl-wrap">
    <table class="tbl stack">
        <thead><tr><th>Nama Tenant</th><th>Penanggung Jawab</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse ($tenants as $t)
            <tr>
                <td data-label="Nama Tenant">{{ $t->nama_tenants }}</td>
                <td data-label="Penanggung Jawab">{{ $t->penanggung_jawab }}</td>
                <td data-label="Status"><span class="badge {{ $t->isActive() ? 'b-green' : 'b-red' }}">{{ $t->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td data-label="Aksi" class="noLabel"><div class="acts">
                    <a href="{{ route('superadmin.tenants.edit', $t) }}">Edit</a>
                    <form method="POST" action="{{ route('superadmin.tenants.toggle', $t) }}" data-confirm="{{ $t->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} tenant {{ $t->nama_tenants }}?">
                        @csrf
                        <button class="linkbtn {{ $t->isActive() ? 'act-red' : 'act-green' }}" type="submit">{{ $t->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                </div></td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada tenant. Klik “+ Tambah Tenant”.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $tenants->links() }}
@endsection
