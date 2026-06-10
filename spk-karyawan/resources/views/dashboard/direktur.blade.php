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
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
    <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-users"></i> Total Karyawan
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $totalKaryawan }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan aktif</div>
    </div>
    <div style="background:linear-gradient(135deg,#0891b2,#0e7490);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-calendar-check"></i> Periode Selesai
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $totalPeriode }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">periode</div>
    </div>
    <div style="background:{{ $periodeAktif ? 'linear-gradient(135deg,#16a34a,#15803d)' : 'linear-gradient(135deg,#64748b,#475569)' }};border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-calendar"></i> Periode Aktif
        </div>
        <div style="font-size:18px;font-weight:800;line-height:1.2">{{ $labelAktif ?? 'Tidak Ada' }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">{{ $periodeAktif ? 'sedang berjalan' : 'tidak ada' }}</div>
    </div>
    <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-list-check"></i> Jumlah Kriteria
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $totalKriteria }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">kriteria penilaian</div>
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

<div class="row g-3">
    {{-- Karyawan Terbaik Top 5 --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header" style="justify-content:space-between">
                <div style="display:flex;flex-direction:column;gap:2px">
                    <span style="font-size:16px;font-weight:700"><i class="ti ti-trophy"></i> Karyawan Terbaik (Top 5)</span>
                    @if($periodeTerakhir)
                    <span style="font-size:11px;color:#64748b;font-weight:500">Periode {{ ($namaBulan[$periodeTerakhir->bulan] ?? $periodeTerakhir->bulan).' '.$periodeTerakhir->tahun }}</span>
                    @endif
                </div>
                @if($periodeTerakhir)
                <a href="{{ route('ranking.hasil', $periodeTerakhir) }}" class="btn btn-sm btn-info-soft">Lihat semua</a>
                @endif
            </div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;color:#475569;font-weight:700" class="text-center">No.</th>
                        <th style="color:#475569;font-weight:700">Karyawan</th>
                        <th style="color:#475569;font-weight:700">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topKaryawan as $h)
                    <tr>
                        <td class="text-center" style="font-weight:800;color:#1e293b;font-size:15px">{{ $h->ranking }}</td>
                        <td>
                            <div style="font-weight:600;color:#1e293b;font-size:13px">{{ $h->karyawan?->nama ?? '—' }}</div>
                            <div style="font-size:11px;color:#94a3b8">ID: {{ $h->karyawan?->id ?? '—' }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="flex:1;height:7px;background:#e2e8f0;border-radius:4px;overflow:hidden;min-width:60px">
                                    <div style="height:100%;width:{{ round(($h->nilai_preferensi ?? 0)*100) }}%;background:linear-gradient(90deg,#6366f1,#2563eb);border-radius:4px"></div>
                                </div>
                                <span style="font-weight:800;color:#1e293b;font-size:13px;flex-shrink:0">{{ number_format($h->nilai_preferensi, 3) }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;padding:32px;color:#94a3b8">
                            <i class="ti ti-trophy-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
                            Belum ada periode yang selesai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Distribusi Kriteria --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header" style="justify-content:flex-start">
                <span style="font-size:16px;font-weight:700"><i class="ti ti-chart-pie"></i> Distribusi Kriteria</span>
            </div>
            <div style="padding:14px 18px">
                @forelse($kriteriaList as $k)
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
                <div style="text-align:center;padding:24px;color:#94a3b8">Belum ada kriteria.</div>
                @endforelse
                @if($kriteriaList->count())
                <div style="display:flex;justify-content:space-between;border-top:0.5px solid #f1f5f9;padding-top:10px;margin-top:4px;font-size:13px">
                    <span style="color:#64748b;font-weight:600">Total Bobot</span>
                    <span style="font-weight:800;color:#1e293b">{{ rtrim(rtrim(number_format($kriteriaList->sum('bobot'),2),'0'),'.') }}%</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection