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

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:12px">
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-users"></i> Total Pengguna</div>
        <div class="stat-val" style="font-size:22px">{{ $pengguna->total() }}</div>
    </div>
    <div class="stat-card" style="border-color:#93c5fd;background:#eff6ff">
        <div class="stat-lbl" style="color:#1d4ed8">Direktur</div>
        <div class="stat-val" style="font-size:22px;color:#1d4ed8">{{ $pengguna->getCollection()->where('role','direktur')->count() }}</div>
    </div>
    <div class="stat-card" style="border-color:#d1d5db;background:#f9fafb">
        <div class="stat-lbl" style="color:#374151">Admin</div>
        <div class="stat-val" style="font-size:22px;color:#374151">{{ $pengguna->getCollection()->where('role','admin')->count() }}</div>
    </div>
    <div class="stat-card" style="border-color:#86efac;background:#f0fdf4">
        <div class="stat-lbl" style="color:#16a34a">Karyawan</div>
        <div class="stat-val" style="font-size:22px;color:#16a34a">{{ $pengguna->getCollection()->where('role','karyawan')->count() }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="ti ti-users"></i> Daftar Pengguna Sistem</span>
        <span style="font-size:11px;color:#64748b">{{ $pengguna->total() }} pengguna terdaftar</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Pengguna</th>
                <th style="width:100px">Role</th>
                <th style="width:80px" class="text-center">Status</th>
                <th style="width:80px" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengguna as $p)
            @php
                $nonaktif    = $p->karyawan && $p->karyawan->status === 'tidak_aktif';
                $namaDisplay = $p->karyawan ? $p->karyawan->nama : ucwords(str_replace(['_','.','@'],[' ',' ',' '],$p->username));
                $subDisplay  = $p->karyawan ? $p->karyawan->divisi : ucfirst($p->role);
                $avatarBg    = $p->role=='direktur' ? '#dbeafe' : ($p->role=='admin' ? '#f3f4f6' : '#dcfce7');
                $avatarColor = $p->role=='direktur' ? '#1d4ed8' : ($p->role=='admin' ? '#374151' : '#16a34a');
            @endphp
            <tr>
                <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>

                {{-- Pengguna --}}
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;background:{{ $avatarBg }};color:{{ $avatarColor }}">
                            {{ strtoupper(substr($namaDisplay,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;color:#1e293b;font-size:13px">
                                {{ $namaDisplay }}
                                @if($p->id === auth()->id())
                                <span style="font-size:9px;background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:1px 5px;margin-left:4px">Anda</span>
                                @endif
                            </div>
                            <div style="font-size:11px;color:#94a3b8">
                                <i class="ti ti-at" style="font-size:10px"></i> {{ $p->username }}
                                &nbsp;·&nbsp;
                                <i class="ti ti-briefcase" style="font-size:10px"></i> {{ $subDisplay }}
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Role --}}
                <td>
                    <span class="badge {{ $p->role=='direktur'?'bg-info-soft':($p->role=='admin'?'bg-gray-soft':'bg-success-soft') }}">
                        {{ ucfirst($p->role) }}
                    </span>
                </td>

                {{-- Status --}}
                <td class="text-center">
                    @if($nonaktif)
                    <span class="badge bg-danger-soft">Nonaktif</span>
                    @else
                    <span class="badge bg-success-soft">Aktif</span>
                    @endif
                </td>

                {{-- Aksi --}}
                <td class="text-center">
                    @if(auth()->user()->role === 'admin' && $p->role === 'direktur')
                    <span style="font-size:11px;color:#94a3b8">—</span>
                    @else
                    <a href="{{ route('pengguna.edit', $p) }}" class="btn btn-sm"
                        style="background:#2563eb;border-color:#2563eb;color:#fff">
                        <i class="ti ti-pencil"></i> Edit
                    </a>
                    @endif
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