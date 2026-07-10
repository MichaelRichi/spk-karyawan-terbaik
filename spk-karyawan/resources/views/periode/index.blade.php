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
    <style>
        .tabel-periode tbody td { border-bottom: 2px solid #d7dee8 !important; vertical-align: middle; }
        .tabel-periode tbody tr:last-child td { border-bottom: none !important; }
    </style>
    <table class="table mb-0 tabel-periode">
        <thead>
            <tr>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-calendar"></i> Periode</span></th>
                <th style="width:110px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-circle-check"></i> Status</span></th>
                <th style="color:#475569;font-weight:700;padding-left:110px"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-info-circle"></i> Info</span></th>
                <th style="width:160px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-settings"></i> Aksi</span></th>
            </tr>
        </thead>
        <tbody>
            @php $tahunSekarang = null; @endphp
            @forelse($periode as $p)
            @php $labelBulan = ($namaBulan[$p->bulan] ?? $p->bulan).' '.$p->tahun; @endphp
            @if($tahunSekarang !== $p->tahun)
            @php $tahunSekarang = $p->tahun; @endphp
            <tr style="background:#f1f5f9">
                <td colspan="4" style="font-weight:800;color:#334155;font-size:12px;letter-spacing:.5px;padding:8px 14px"><i class="ti ti-calendar-stats"></i> Tahun {{ $p->tahun }}</td>
            </tr>
            @endif
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
                        @php
                            $terbaikTetap = $p->hasilRanking->where('tipe','tetap')->where('ranking',1)->first();
                            $terbaikTT    = $p->hasilRanking->where('tipe','tidak_tetap')->where('ranking',1)->first();
                        @endphp
                        <div style="font-size:12px;display:flex;flex-direction:column;gap:5px;max-width:340px">
                            <div style="background:#eff6ff;border-left:3px solid #2563eb;border-radius:8px;padding:5px 10px;display:flex;align-items:center;gap:8px">
                                <span style="color:#2563eb;font-size:9px;font-weight:700;letter-spacing:.5px;white-space:nowrap;display:inline-block;min-width:78px">TETAP</span>
                                <strong style="color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $terbaikTetap?->karyawan?->nama ?? '—' }}</strong>
                                <span style="margin-left:auto;background:#dbeafe;color:#185FA5;font-weight:700;font-size:11px;padding:2px 9px;border-radius:20px;white-space:nowrap">{{ $terbaikTetap ? number_format($terbaikTetap->nilai_preferensi, 3) : '—' }}</span>
                            </div>
                            <div style="background:#f0fdfa;border-left:3px solid #0d9488;border-radius:8px;padding:5px 10px;display:flex;align-items:center;gap:8px">
                                <span style="color:#0d9488;font-size:9px;font-weight:700;letter-spacing:.5px;white-space:nowrap;display:inline-block;min-width:78px">TIDAK TETAP</span>
                                <strong style="color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $terbaikTT?->karyawan?->nama ?? '—' }}</strong>
                                <span style="margin-left:auto;background:#ccfbf1;color:#0f766e;font-weight:700;font-size:11px;padding:2px 9px;border-radius:20px;white-space:nowrap">{{ $terbaikTT ? number_format($terbaikTT->nilai_preferensi, 3) : '—' }}</span>
                            </div>
                        </div>
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