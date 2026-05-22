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
    <div style="font-size:13px;color:#64748b;margin-top:2px">{{ auth()->user()->username }} &nbsp;·&nbsp; Direktur</div>
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
    <div style="background:linear-gradient(135deg,#d97706,#b45309);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-trophy"></i> Karyawan Terbaik
        </div>
        <div style="font-size:16px;font-weight:800;line-height:1.2">{{ $karyawanTerbaik?->karyawan?->nama ?? '—' }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">
            {{ $karyawanTerbaik ? ($namaBulan[$karyawanTerbaik->periode->bulan] ?? '').' '.$karyawanTerbaik->periode->tahun : 'belum ada' }}
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Periode Aktif --}}
    @if($periodeAktif)
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <span><i class="ti ti-calendar-event"></i> Periode Aktif</span>
                <a href="{{ route('penilaian.index', $periodeAktif) }}" class="btn btn-sm" style="background:#2563eb;color:#fff;border-color:#2563eb">Input Nilai</a>
            </div>
            <div style="padding:14px">
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
    </div>
    @endif

    {{-- Riwayat Periode --}}
    <div class="col-md-{{ $periodeAktif ? '7' : '12' }}">
        <div class="card h-100">
            <div class="card-header"><i class="ti ti-clock-hour-4"></i> Riwayat Periode</div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Terbaik</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $p)
                    @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
                    <tr>
                        <td style="font-weight:600">{{ ($namaBulan[$p->bulan] ?? $p->bulan).' '.$p->tahun }}</td>
                        <td><span class="badge {{ $p->status=='selesai'?'bg-success-soft':($p->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}">{{ ucfirst($p->status) }}</span></td>
                        <td style="font-size:13px">{{ $terbaik?->karyawan?->nama ?? '—' }}</td>
                        <td class="text-center" style="color:#185FA5;font-weight:600;font-size:13px">{{ $terbaik ? number_format($terbaik->nilai_preferensi,4) : '—' }}</td>
                        <td class="text-center">
                            @if($p->status === 'selesai')
                            <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-sm btn-info-soft">Hasil</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada periode.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection