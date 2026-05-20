@extends('layouts.app')
@section('title','Kelola Karyawan')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Kelola Karyawan</div>
        <div class="ph-sub">Data seluruh karyawan PT Cempaka Indah Abadi · Hanya karyawan <strong>Aktif</strong> yang dapat dinilai</div>
    </div>
    <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus"></i> Tambah Karyawan
    </a>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="ti ti-id-badge-2"></i> Daftar Karyawan</span>
        <span style="font-size:11px;color:#64748b">
            <span style="color:#27500A;font-weight:600">{{ $karyawan->where('status','aktif')->count() }}</span> aktif ·
            <span style="color:#A32D2D;font-weight:600">{{ $karyawan->where('status','tidak_aktif')->count() }}</span> tidak aktif
        </span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th>#</th><th>Nama</th><th>Divisi</th><th>Jenis Kelamin</th><th>Tgl Masuk</th><th>Status</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr style="{{ $k->status=='tidak_aktif'?'opacity:.6':'' }}">
                <td style="color:#64748b">{{ $loop->iteration }}</td>
                <td style="font-weight:600">
                    {{ $k->nama }}
                    @if($k->status=='tidak_aktif')
                    <span style="font-size:9px;color:#A32D2D;font-weight:400"> (tidak aktif)</span>
                    @endif
                </td>
                <td style="color:#64748b">{{ $k->jabatan }}</td>
                <td>{{ ucfirst($k->jenis_kelamin) }}</td>
                <td style="color:#64748b">{{ $k->tanggal_masuk->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $k->status=='aktif'?'bg-success-soft':'bg-danger-soft' }}">
                        {{ $k->status=='aktif'?'Aktif':'Tidak Aktif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center;align-items:center;flex-wrap:wrap">
                        {{-- Edit Karyawan --}}
                        <a href="{{ route('karyawan.edit', $k) }}" class="btn btn-sm" style="background:#2563eb;border-color:#2563eb;color:#fff">
                            <i class="ti ti-pencil"></i> Edit Karyawan
                        </a>
                        {{-- Akun --}}
                        @if($k->user)
                        <a href="{{ route('karyawan.akun.form', $k) }}" class="btn btn-sm" style="background:#16a34a;border-color:#16a34a;color:#fff">
                            <i class="ti ti-user-check"></i> Edit Akun
                        </a>
                        @else
                        <a href="{{ route('karyawan.akun.form', $k) }}" class="btn btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#fff">
                            <i class="ti ti-user-plus"></i> Buat Akun
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4" style="color:#64748b">Belum ada karyawan.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($karyawan->hasPages())
    <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0">{{ $karyawan->links() }}</div>
    @endif
</div>
@endsection