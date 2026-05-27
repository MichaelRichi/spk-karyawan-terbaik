@extends('layouts.app')
@section('title','Kelola Karyawan')
@section('content')
<div class="card" style="margin-bottom:16px;max-width:750px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Kelola Karyawan</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Data seluruh karyawan PT Cempaka Indah Abadi · Hanya karyawan <strong>Aktif</strong> yang dapat dinilai</div>
        </div>
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
            <i class="ti ti-user-plus"></i> Tambah Karyawan
        </a>
    </div>
</div>

<form method="GET" action="{{ route('karyawan.index') }}" style="margin-bottom:12px;max-width:750px;margin-left:auto;margin-right:auto">
    <div style="display:flex;gap:8px;align-items:center">
        <div style="display:flex;flex:1">
            <div style="position:relative;flex:1">
                <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control" placeholder="Cari nama karyawan..."
                    style="padding-left:32px;border-radius:8px 0 0 8px;border-right:none">
            </div>
            <button type="submit" class="btn btn-primary" style="border-radius:0 8px 8px 0;padding:8px 14px;flex-shrink:0">
                <i class="ti ti-search"></i>
            </button>
        </div>
        <div style="position:relative;width:180px;flex-shrink:0">
            <select name="status" class="form-select" style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif"       {{ request('status')=='aktif'      ?'selected':'' }}>Aktif</option>
                <option value="tidak_aktif" {{ request('status')=='tidak_aktif'?'selected':'' }}>Tidak Aktif</option>
            </select>
            <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
        </div>
        @if(request('search') || request('status'))
        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-x"></i> Reset
        </a>
        @endif
    </div>
</form>

<div class="card" style="max-width:750px;margin-left:auto;margin-right:auto">
    <div class="card-header">
        <span><i class="ti ti-id-badge-2"></i> Daftar Karyawan</span>
        <span style="font-size:11px;color:#64748b">
            <span style="color:#27500A;font-weight:600">{{ $karyawan->where('status','aktif')->count() }}</span> aktif ·
            <span style="color:#A32D2D;font-weight:600">{{ $karyawan->where('status','tidak_aktif')->count() }}</span> tidak aktif
        </span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th>#</th><th>Nama</th><th>Tgl Lahir</th><th>Tgl Masuk</th><th>Status</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr style="{{ $k->status=='tidak_aktif'?'opacity:.6':'' }}">
                <td style="color:#64748b">{{ $loop->iteration }}</td>
                <td>
                    <div style="font-weight:600;color:#1e293b">{{ $k->nama }}</div>
                    
                    @if($k->status=='tidak_aktif')
                    <span style="font-size:9px;color:#A32D2D;font-weight:400">(tidak aktif)</span>
                    @endif
                </td>
                <td>{{ ucfirst($k->jenis_kelamin) }}</td>
                <td style="color:#64748b">{{ $k->tgl_masuk->format('d/m/Y') }}</td>
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