@extends('layouts.app')
@section('title','Kelola Pengguna')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Kelola Pengguna</div>
        <div class="ph-sub">Manajemen akun Admin, Direktur, dan Karyawan</div>
    </div>
    <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus"></i> Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="card-header"><i class="ti ti-users"></i> Daftar Pengguna Sistem</div>
    <table class="table mb-0">
        <thead>
            <tr><th>#</th><th>Username</th><th>Role</th><th>Karyawan</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($pengguna as $p)
            <tr>
                <td style="color:#64748b">{{ $loop->iteration }}</td>
                <td style="font-weight:600">{{ $p->username }}</td>
                <td>
                    <span class="badge {{ $p->role=='direktur'?'bg-info-soft':($p->role=='admin'?'bg-gray-soft':'bg-success-soft') }}">
                        {{ ucfirst($p->role) }}
                    </span>
                </td>
                <td style="color:#64748b">{{ optional($p->karyawan)->nama ?? '—' }}</td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('pengguna.edit', $p) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-pencil"></i>
                        </a>
                        @if($p->id !== auth()->id())
                        <form action="{{ route('pengguna.destroy', $p) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus pengguna {{ $p->username }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4" style="color:#64748b">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($pengguna->hasPages())
    <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0">{{ $pengguna->links() }}</div>
    @endif
</div>
@endsection