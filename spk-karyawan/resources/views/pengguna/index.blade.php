@extends('layouts.app')
@section('title','Kelola Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <span></span>
    <a href="{{ route('pengguna.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-user-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card border-0 shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Role</th>
                <th>Karyawan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengguna as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $p->username }}</td>
                <td>
                    <span class="badge bg-{{ $p->role=='direktur'?'primary':($p->role=='admin'?'secondary':'success') }}">
                        {{ $p->role }}
                    </span>
                </td>
                <td>{{ optional($p->karyawan)->nama ?? '—' }}</td>
                <td class="text-center">
                    <a href="{{ route('pengguna.edit', $p) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-pencil"></i>
                    </a>
                    @if($p->id !== auth()->id())
                    <form action="{{ route('pengguna.destroy', $p) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Hapus pengguna {{ $p->username }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Belum ada pengguna.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($pengguna->hasPages())
    <div class="card-footer bg-white">{{ $pengguna->links() }}</div>
    @endif
</div>
@endsection