@extends('layouts.panel')
@section('title', $admin->exists ? 'Edit Admin Tenant' : 'Tambah Admin Tenant')
@section('content')
<form class="card form" method="POST" action="{{ $admin->exists ? route('superadmin.admin.update', $admin) : route('superadmin.admin.store') }}" data-once autocomplete="off">
    @csrf @if ($admin->exists) @method('PUT') @endif

    <div class="field"><label for="tenant_id">Tenant</label>
        @if ($admin->exists)
            <input class="input" value="{{ $admin->tenant?->nama_tenants }}" disabled>
        @else
            <select class="input @error('tenant_id') invalid @enderror" id="tenant_id" name="tenant_id" required>
                <option value="">— pilih tenant —</option>
                @foreach ($tenants as $t)<option value="{{ $t->id }}" @selected(old('tenant_id') == $t->id)>{{ $t->nama_tenants }}{{ $t->isActive() ? '' : ' (nonaktif)' }}</option>@endforeach
            </select>
            @error('tenant_id')<div class="err">{{ $message }}</div>@enderror
        @endif
    </div>
    <div class="field"><label for="nama">Nama</label>
        <input class="input @error('nama') invalid @enderror" id="nama" name="nama" value="{{ old('nama', $admin->nama) }}" maxlength="100" required>
        @error('nama')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="row2">
        <div class="field"><label for="email">Email</label>
            <input class="input @error('email') invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $admin->email) }}" maxlength="150" required>
            @error('email')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="username">Username</label>
            <input class="input @error('username') invalid @enderror" id="username" name="username" value="{{ old('username', $admin->username) }}" maxlength="50" required autocapitalize="none">
            @error('username')<div class="err">{{ $message }}</div>@enderror
            <div class="hint">Huruf kecil, angka, titik, strip (3–50 karakter).</div></div>
    </div>
    <div class="field"><label for="password">Kata Sandi {{ $admin->exists ? '(kosongkan bila tidak diganti)' : '' }}</label>
        <input class="input @error('password') invalid @enderror" id="password" name="password" type="password" minlength="8" maxlength="100" autocomplete="new-password" {{ $admin->exists ? '' : 'required' }}>
        @error('password')<div class="err">{{ $message }}</div>@enderror
        <div class="hint">Minimal 8 karakter.</div></div>
    @if ($admin->exists)
    <div class="field"><label for="status">Status</label>
        <select class="input" id="status" name="status">
            <option value="aktif" @selected(old('status', $admin->status) === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(old('status', $admin->status) === 'nonaktif')>Nonaktif</option>
        </select></div>
    @endif
    <div class="acts"><button class="btn orange" type="submit">Simpan</button><a class="btn ghost" href="{{ route('superadmin.admin') }}">Batal</a></div>
</form>
@endsection
