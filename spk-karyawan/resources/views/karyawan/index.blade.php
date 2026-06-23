@extends('layouts.app')
@section('title','Kelola Karyawan')
@section('content')
<div class="card" style="margin-bottom:16px;max-width:1150px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Kelola Karyawan</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Data seluruh karyawan PT Cempaka Indah Abadi · Hanya karyawan <strong>Aktif</strong> yang dapat dinilai</div>
        </div>
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary" style="padding:8px 14px;font-size:12px;font-weight:600">
            <i class="ti ti-user-plus"></i> Tambah Karyawan
        </a>
    </div>
</div>

<form method="GET" action="{{ route('karyawan.index') }}" style="margin-bottom:12px;max-width:1150px;margin-left:auto;margin-right:auto">
    <div style="display:flex;gap:8px;align-items:center">
        <div style="display:flex;flex:1">
            <div style="position:relative;flex:1">
                <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control" placeholder="Cari nama karyawan..."
                    style="padding:8px 12px 8px 32px;font-size:13px;border-radius:8px 0 0 8px;border-right:none">
            </div>
            <button type="submit" class="btn btn-primary" style="border-radius:0 8px 8px 0;padding:8px 13px;flex-shrink:0">
                <i class="ti ti-search"></i>
            </button>
        </div>
        <div style="position:relative;width:180px;flex-shrink:0">
            <select name="status" class="form-select" style="appearance:none;-webkit-appearance:none;padding:8px 32px 8px 12px;font-size:13px;cursor:pointer" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif"       {{ request('status')=='aktif'      ?'selected':'' }}>Aktif</option>
                <option value="tidak_aktif" {{ request('status')=='tidak_aktif'?'selected':'' }}>Tidak Aktif</option>
            </select>
            <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
        </div>
    </div>
</form>

<div class="card" style="max-width:1150px;margin-left:auto;margin-right:auto">
    <div class="card-header">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-id-badge-2"></i> Daftar Karyawan</span>
        <span style="font-size:13px;color:#64748b">
            <span style="color:#27500A;font-weight:600">{{ $karyawan->where('status','aktif')->count() }}</span> Aktif ·
            <span style="color:#A32D2D;font-weight:600">{{ $karyawan->where('status','tidak_aktif')->count() }}</span> Tidak Aktif
        </span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="color:#475569;font-weight:700">No.</th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-user"></i> Nama</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-users"></i> Jenis Kelamin</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-calendar"></i> Tanggal Masuk</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-clock"></i> Masa Kerja</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-circle-check"></i> Status</span></th>
                <th class="text-center" style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-settings"></i> Aksi</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawan as $k)
            <tr>
                <td style="color:#475569;font-size:13px;font-weight:600">{{ $loop->iteration }}</td>
                <td>
                    <div style="font-weight:700;color:#1e293b;font-size:14px">{{ $k->nama }}</div>
                    <div style="font-size:11px;color:#94a3b8;font-weight:500">ID: {{ $k->id }}</div>
                    
                    @if($k->status=='tidak_aktif')
                    <span style="font-size:9px;color:#A32D2D;font-weight:400">(tidak aktif)</span>
                    @endif
                </td>
                <td style="color:#475569;font-size:13px">{{ $k->jenis_kelamin }}</td>
                <td style="color:#475569;font-size:13px">
                    @if($k->tgl_masuk)
                    @php $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                    {{ $k->tgl_masuk->format('d') }} {{ $nb[(int)$k->tgl_masuk->format('n')] }} {{ $k->tgl_masuk->format('Y') }}
                    @else —
                    @endif
                </td>
                <td style="color:#374151;font-size:13px">
                    @if($k->tgl_masuk)
                    @php
                        $bulan = $k->tgl_masuk->diffInMonths(now());
                        $thn = floor($bulan / 12);
                        $bln = $bulan % 12;
                    @endphp
                    {{ $thn > 0 ? $thn.' tahun' : '' }}{{ $bln > 0 ? ' '.$bln.' bulan' : '' }}{{ $thn == 0 && $bln == 0 ? '< 1 bulan' : '' }}
                    @else —
                    @endif
                </td>
                <td>
                    <span class="badge {{ $k->status=='aktif'?'bg-success-soft':'bg-danger-soft' }}" style="font-size:12px;padding:4px 11px">
                        {{ $k->status=='aktif'?'Aktif':'Tidak Aktif' }}
                    </span>
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center;align-items:center;flex-wrap:wrap">
                        {{-- Edit Karyawan --}}
                        <a href="{{ route('karyawan.edit', $k) }}" class="btn btn-sm" style="background:#2563eb;border-color:#2563eb;color:#fff;font-size:11px;font-weight:600;padding:5px 10px">
                            <i class="ti ti-pencil"></i> Edit Karyawan
                        </a>
                        {{-- Akun --}}
                        @if($k->user)
                        <a href="{{ route('karyawan.akun.form', $k) }}" class="btn btn-sm" style="background:#16a34a;border-color:#16a34a;color:#fff;font-size:11px;font-weight:600;padding:5px 10px">
                            <i class="ti ti-user-check"></i> Edit Akun
                        </a>
                        @else
                        <a href="{{ route('karyawan.akun.form', $k) }}" class="btn btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#fff;font-size:11px;font-weight:600;padding:5px 10px">
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