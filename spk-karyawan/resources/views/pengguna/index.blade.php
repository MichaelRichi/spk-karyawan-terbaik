@extends('layouts.app')
@section('title','Kelola Pengguna')
@section('content')

{{-- Card Judul --}}
<div class="card" style="margin-bottom:16px;max-width:800px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Kelola Pengguna</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Manajemen akun Admin, Direktur, dan Karyawan</div>
        </div>
        <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
            <i class="ti ti-user-plus"></i> Tambah Pengguna
        </a>
    </div>
</div>

{{-- Search + Filter --}}
<div style="max-width:800px;margin:0 auto 12px auto">
    <form method="GET" action="{{ route('pengguna.index') }}">
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <div style="display:flex;flex:1;min-width:180px">
                <div style="position:relative;flex:1">
                    <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" placeholder="Cari pengguna..."
                        style="padding-left:32px;border-radius:8px 0 0 8px;border-right:none">
                </div>
                <button type="submit" class="btn btn-primary" style="border-radius:0 8px 8px 0;padding:8px 14px;flex-shrink:0">
                    <i class="ti ti-search"></i>
                </button>
            </div>
            <div style="position:relative;width:150px">
                <select name="role" class="form-select" style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    <option value="direktur" {{ request('role')=='direktur'?'selected':'' }}>Direktur</option>
                    <option value="admin"    {{ request('role')=='admin'   ?'selected':'' }}>Admin</option>
                    <option value="karyawan" {{ request('role')=='karyawan'?'selected':'' }}>Karyawan</option>
                </select>
                <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
            </div>
            @if(request('search') || request('role'))
            <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-x"></i> Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="card" style="max-width:800px;margin:0 auto">
    <div class="card-header">
        <span><i class="ti ti-users"></i> Daftar Pengguna Sistem</span>
        <span style="font-size:11px;color:#64748b">{{ $pengguna->total() }} pengguna terdaftar</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="width:40px;color:#374151;font-weight:700">No.</th>
                <th style="color:#374151;font-weight:700">Pengguna</th>
                <th style="width:100px;color:#374151;font-weight:700">Role</th>
                <th style="width:80px;color:#374151;font-weight:700" class="text-center">Status</th>
                <th style="width:80px;color:#374151;font-weight:700" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengguna as $p)
            @php
                $nonaktif    = $p->karyawan && $p->karyawan->status === 'tidak_aktif';
                $namaDisplay = $p->karyawan ? $p->karyawan->nama : ucwords(str_replace(['_','.','@'],[' ',' ',' '],$p->username));
                $subDisplay  = ucfirst($p->role);
                $avatarBg    = $p->role=='direktur' ? '#dbeafe' : ($p->role=='admin' ? '#f3f4f6' : '#dcfce7');
                $avatarColor = $p->role=='direktur' ? '#1d4ed8' : ($p->role=='admin' ? '#374151' : '#16a34a');
            @endphp
            <tr>
                <td style="color:#374151;font-size:13px;font-weight:600">{{ $loop->iteration }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;background:{{ $avatarBg }};color:{{ $avatarColor }}">
                            {{ strtoupper(substr($namaDisplay,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:#1e293b;font-size:13px">
                                {{ $namaDisplay }}
                                @if($p->id === auth()->id())
                                <span style="font-size:9px;background:#dbeafe;color:#1d4ed8;border-radius:4px;padding:1px 5px;margin-left:4px">Anda</span>
                                @endif
                            </div>
                            <div style="font-size:11px;color:#64748b">{{ $p->username }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $p->role=='direktur'?'bg-info-soft':($p->role=='admin'?'bg-gray-soft':'bg-success-soft') }}">
                        {{ ucfirst($p->role) }}
                    </span>
                </td>
                <td class="text-center">
                    @if($nonaktif)
                    <span class="badge bg-danger-soft">Nonaktif</span>
                    @else
                    <span class="badge bg-success-soft">Aktif</span>
                    @endif
                </td>
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