@extends('layouts.app')
@section('title','Kelola Karyawan')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Kelola Karyawan</div>
        <div class="ph-sub">Data seluruh karyawan PT Cempaka Indah Abadi</div>
    </div>
    <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus"></i> Tambah Karyawan
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="ti ti-id-badge-2"></i> Daftar Karyawan</span>
        <span style="font-size:11px;color:#64748b">Total: {{ $karyawan->total() }} karyawan</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th>#</th><th>Nama</th><th>Jabatan</th><th>Jenis Kelamin</th><th>Tgl Masuk</th><th>Status</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr>
                <td style="color:#64748b">{{ $loop->iteration }}</td>
                <td style="font-weight:600">{{ $k->nama }}</td>
                <td style="color:#64748b">{{ $k->jabatan }}</td>
                <td>{{ ucfirst($k->jenis_kelamin) }}</td>
                <td style="color:#64748b">{{ $k->tanggal_masuk->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $k->status=='tetap'?'bg-success-soft':'bg-warning-soft' }}">
                        {{ ucfirst(str_replace('_',' ',$k->status)) }}
                    </span>
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('karyawan.edit', $k) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <form action="{{ route('karyawan.destroy', $k) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus karyawan {{ $k->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4" style="color:#64748b">Belum ada karyawan. <a href="{{ route('karyawan.create') }}">Tambah sekarang</a></td></tr>
            @endforelse
        </tbody>
    </table>
    @if($karyawan->hasPages())
    <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0">{{ $karyawan->links() }}</div>
    @endif
</div>
@endsection