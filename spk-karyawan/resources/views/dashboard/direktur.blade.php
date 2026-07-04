@extends('layouts.app')
@section('title','Dashboard')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$labelAktif = $periodeAktif ? ($namaBulan[$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun : null;
$namaUser = auth()->user()->karyawan?->nama ?? auth()->user()->username;
@endphp

{{-- Greeting --}}
<div style="margin-bottom:20px">
    <div style="font-size:22px;font-weight:800;color:#1e293b">
        Selamat Datang, <span style="color:#2563eb">{{ $namaUser }}</span>!
    </div>

</div>

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
    <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-check"></i> Karyawan Tetap
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $karyawanTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan aktif</div>
    </div>
    <div style="background:linear-gradient(135deg,#4f46e5,#4338ca);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-plus"></i> Karyawan Tidak Tetap
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $karyawanTidakTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan aktif</div>
    </div>
    <div style="background:linear-gradient(135deg,#0891b2,#0e7490);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-calendar-check"></i> Periode Selesai
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $totalPeriode }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">periode</div>
    </div>
    <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-list-check"></i> Kriteria Karyawan Tetap
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $kriteriaTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">kriteria penilaian</div>
    </div>
    <div style="background:linear-gradient(135deg,#a21caf,#86198f);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-list-check"></i> Kriteria Karyawan Tidak Tetap
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $kriteriaTidakTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">kriteria penilaian</div>
    </div>
    <div style="background:{{ $periodeAktif ? 'linear-gradient(135deg,#16a34a,#15803d)' : 'linear-gradient(135deg,#64748b,#475569)' }};border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-calendar"></i> Periode Aktif
        </div>
        <div style="font-size:18px;font-weight:800;line-height:1.2">{{ $labelAktif ?? 'Tidak Ada' }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">{{ $periodeAktif ? 'sedang berjalan' : 'tidak ada' }}</div>
    </div>
</div>

{{-- Periode Aktif --}}
@if($periodeAktif)
<div class="card" style="margin-bottom:16px">
    <div class="card-header" style="justify-content:space-between">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-calendar-event"></i> Periode Aktif</span>
        <a href="{{ route('penilaian.index', $periodeAktif) }}" class="btn btn-sm" style="background:#2563eb;color:#fff;border-color:#2563eb">Input Nilai</a>
    </div>
    <div style="padding:14px 18px">
        <div style="font-size:14px;font-weight:700;color:#1e293b;margin-bottom:10px">{{ $labelAktif }}</div>
        @php $dinilai = $periodeAktif->penilaian->pluck('karyawan_id')->unique()->count(); $persen = $totalKaryawan>0?round($dinilai/$totalKaryawan*100):0; @endphp
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
            <span style="color:#64748b">Progress Penilaian</span>
            <span style="font-weight:700;color:{{ $dinilai==$totalKaryawan?'#16a34a':'#f59e0b' }}">{{ $dinilai }}/{{ $totalKaryawan }}</span>
        </div>
        <div style="height:10px;background:#e2e8f0;border-radius:5px;overflow:hidden">
            <div style="height:100%;width:{{ $persen }}%;background:{{ $persen==100?'#16a34a':'#2563eb' }};border-radius:5px"></div>
        </div>
    </div>
</div>
@endif

@foreach(['tetap','tidak_tetap'] as $tp)
@php
    $lbl = $tp==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap';
    $badgeClass = $tp=='tetap'?'bg-info-soft':'bg-warning-soft';
    $topList = $topPerTipe[$tp] ?? collect();
    $kritList = $kriteriaList->where('tipe',$tp);
@endphp
<div class="row g-3" style="{{ !$loop->first ? 'margin-top:4px' : '' }}">
    {{-- Karyawan Terbaik (per tipe) --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header" style="justify-content:space-between">
                <span style="font-size:16px;font-weight:700"><i class="ti ti-trophy"></i> {{ $lbl }} Terbaik</span>
                @if($periodeTerakhir)
                <a href="{{ route('ranking.hasil', $periodeTerakhir) }}" class="btn btn-sm btn-info-soft">Lihat semua</a>
                @endif
            </div>
            @if(!$periodeTerakhir)
            <div style="text-align:center;padding:32px;color:#94a3b8">
                <i class="ti ti-trophy-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
                Belum ada periode yang selesai.
            </div>
            @else
            @forelse($topList as $h)
            @php
                $medal = $h->ranking==1 ? 'linear-gradient(135deg,#fbbf24,#f59e0b)'
                       : ($h->ranking==2 ? 'linear-gradient(135deg,#cbd5e1,#94a3b8)'
                       : ($h->ranking==3 ? 'linear-gradient(135deg,#e0a06a,#b45309)' : '#2563eb'));
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:9px 16px;{{ !$loop->last?'border-bottom:1px solid #f8fafc':'' }}">
                <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;background:{{ $medal }}">{{ $h->ranking }}</div>
                <div style="width:130px;flex-shrink:0">
                    <div style="font-weight:600;color:#1e293b;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $h->karyawan?->nama ?? '—' }}</div>
                    <div style="font-size:11px;color:#94a3b8">ID: {{ $h->karyawan?->id ?? '—' }}</div>
                </div>
                <div style="flex:1;display:flex;align-items:center;gap:10px;min-width:0">
                    <div style="flex:1;height:7px;background:#e2e8f0;border-radius:4px;overflow:hidden;min-width:50px">
                        <div style="height:100%;width:{{ round(($h->nilai_preferensi ?? 0)*100) }}%;background:linear-gradient(90deg,#6366f1,#2563eb);border-radius:4px"></div>
                    </div>
                    <span style="font-weight:800;color:#1e293b;font-size:13px;flex-shrink:0;width:46px;text-align:right">{{ number_format($h->nilai_preferensi, 3) }}</span>
                </div>
            </div>
            @empty
            <div style="padding:24px 16px;text-align:center;color:#94a3b8;font-size:12px">Belum ada ranking {{ $lbl }}.</div>
            @endforelse
            @endif
        </div>
    </div>

    {{-- Distribusi Kriteria (per tipe) --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header" style="justify-content:flex-start">
                <span style="font-size:16px;font-weight:700"><i class="ti ti-chart-pie"></i> Distribusi Kriteria {{ $lbl }}</span>
            </div>
            <div style="padding:14px 18px">
                @forelse($kritList as $k)
                @php $benefit = $k->jenis === 'benefit'; @endphp
                <div style="margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                        <span style="font-size:13px;font-weight:600;color:#1e293b">
                            {{ $k->nama }}
                            <span class="badge {{ $benefit?'bg-success-soft':'bg-danger-soft' }}" style="font-size:10px;padding:2px 7px;margin-left:4px">{{ ucfirst($k->jenis) }}</span>
                        </span>
                        <span style="font-size:13px;font-weight:800;color:#2563eb">{{ rtrim(rtrim(number_format($k->bobot,2),'0'),'.') }}%</span>
                    </div>
                    <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden">
                        <div style="height:100%;width:{{ $k->bobot }}%;background:{{ $benefit?'linear-gradient(90deg,#22c55e,#16a34a)':'linear-gradient(90deg,#f59e0b,#d97706)' }};border-radius:4px"></div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:24px;color:#94a3b8;font-size:12px">Belum ada kriteria {{ $lbl }}.</div>
                @endforelse
                @if($kritList->count())
                <div style="display:flex;justify-content:space-between;border-top:0.5px solid #f1f5f9;padding-top:10px;margin-top:2px;font-size:12px">
                    <span style="color:#64748b;font-weight:600">Total Bobot</span>
                    <span style="font-weight:800;color:{{ $kritList->sum('bobot')==100?'#16a34a':'#dc2626' }}">{{ rtrim(rtrim(number_format($kritList->sum('bobot'),2),'0'),'.') }}%</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection