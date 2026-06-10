@extends('layouts.app')
@section('title','Penilaian')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Penilaian</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Kelola periode penilaian karyawan</div>
        </div>
        <a href="{{ route('periode.create') }}" class="btn btn-primary" style="padding:8px 14px;font-size:12px;font-weight:600">
            <i class="ti ti-plus"></i> Buat Periode Baru
        </a>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('periode.index') }}" style="margin-bottom:12px">
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        {{-- Cari bulan --}}
        <div style="position:relative;min-width:180px;flex:1;max-width:250px">
            <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                class="form-control" placeholder="Cari bulan atau tahun..."
                style="padding-left:32px">
        </div>
        {{-- Filter tahun --}}
        <div style="position:relative;width:140px">
        <select name="tahun" class="form-select" style="width:140px;appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ request('tahun')==$t?'selected':'' }}>{{ $t }}</option>
            @endforeach
        </select>
        <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
        </div>
        {{-- Filter status --}}
        <div style="position:relative;width:150px">
        <select name="status" class="form-select" style="width:150px;appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status')=='aktif'   ?'selected':'' }}>Aktif</option>
            <option value="selesai"  {{ request('status')=='selesai' ?'selected':'' }}>Selesai</option>
            <option value="draft"    {{ request('status')=='draft'   ?'selected':'' }}>Draft</option>
        </select>
        <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
        </div>
        <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Cari</button>
    </div>
</form>

{{-- Daftar Periode --}}
<div class="card">
    <div class="card-header">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-calendar"></i> Daftar Periode</span>
        <span style="font-size:13px;color:#64748b">{{ $periode->total() }} Periode</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-calendar"></i> Periode</span></th>
                <th style="width:110px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-circle-check"></i> Status</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-info-circle"></i> Info</span></th>
                <th style="width:160px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-settings"></i> Aksi</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse($periode as $p)
            @php $labelBulan = ($namaBulan[$p->bulan] ?? $p->bulan).' '.$p->tahun; @endphp
            <tr>
                <td style="font-weight:600;font-size:14px;color:#1e293b">{{ $labelBulan }}</td>
                <td class="text-center">
                    <span class="badge {{ $p->status=='selesai'?'bg-success-soft':($p->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}" style="font-size:12px;padding:4px 11px">
                        {{ $p->status=='aktif' ? 'Aktif' : ($p->status=='selesai' ? 'Selesai ✓' : 'Draft') }}
                    </span>
                </td>
                <td style="font-size:12px;color:#64748b">
                    @if($p->status === 'aktif')
                        @php $dinilai = $p->penilaian->pluck('karyawan_id')->unique()->count(); $total = \App\Models\Karyawan::aktif()->count(); @endphp
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="flex:1;max-width:120px;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:{{ $total>0?($dinilai/$total*100):0 }}%;background:#2563eb;border-radius:3px"></div>
                            </div>
                            <span style="font-weight:600;color:{{ $dinilai==$total?'#16a34a':'#854F0B' }}">{{ $dinilai }}/{{ $total }} karyawan</span>
                        </div>
                    @elseif($p->status === 'selesai')
                        @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
                        Terbaik: <strong style="color:#1e293b">{{ $terbaik?->karyawan?->nama ?? '—' }}</strong>
                        · Nilai: <strong style="color:#185FA5">{{ $terbaik ? number_format($terbaik->nilai_preferensi, 3) : '—' }}</strong>
                    @else
                        <span style="color:#94a3b8">Menunggu aktivasi</span>
                    @endif
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('periode.show', $p) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-eye"></i>
                        </a>
                        @if($p->status === 'aktif')
                        <a href="{{ route('penilaian.index', $p) }}" class="btn btn-sm" style="background:#2563eb;color:#fff;border-color:#2563eb">
                            <i class="ti ti-pencil-check"></i> Input
                        </a>
                        @endif
                        @if($p->status === 'selesai')
                        <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-sm" style="background:#16a34a;color:#fff;border-color:#16a34a">
                            <i class="ti ti-trophy"></i> Hasil
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-4" style="color:#64748b">
                    <i class="ti ti-calendar-off" style="font-size:28px;display:block;margin-bottom:6px"></i>
                    Belum ada periode yang sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($periode->hasPages())
    <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0">{{ $periode->links() }}</div>
    @endif
</div>

@endsection