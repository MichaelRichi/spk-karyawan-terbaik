@extends('layouts.app')
@section('title','Kelola Karyawan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control form-control-sm"
            placeholder="Cari nama/jabatan..." value="{{ request('search') }}">
        <button class="btn btn-sm btn-outline-secondary">Cari</button>
        @if(request('search'))
        <a href="{{ route('karyawan.index') }}" class="btn btn-sm btn-outline-danger">Reset</a>
        @endif
    </form>
    <a href="{{ route('karyawan.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i> Tambah Karyawan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Jenis Kelamin</th>
                <th>Tgl Masuk</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $k->nama }}</td>
                <td>{{ $k->jabatan }}</td>
                <td>{{ ucfirst($k->jenis_kelamin) }}</td>
                <td>{{ $k->tanggal_masuk->format('d/m/Y') }}</td>
                <td>
                    <span class="badge bg-{{ $k->status=='tetap'?'success':'warning text-dark' }}">
                        {{ $k->status }}
                    </span>
                </td>
                <td class="text-center">
                    <a href="{{ route('karyawan.edit', $k) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-pencil"></i>
                    </a>
                    <form action="{{ route('karyawan.destroy', $k) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Hapus karyawan {{ $k->nama }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="ti ti-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    Belum ada karyawan.
                    <a href="{{ route('karyawan.create') }}">Tambah sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($karyawan->hasPages())
    <div class="card-footer bg-white">{{ $karyawan->links() }}</div>
    @endif
</div>
@endsection