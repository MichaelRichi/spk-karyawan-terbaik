@extends('layouts.app')
@section('title','Dashboard')
@section('content')

@php
$nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$namaUser = auth()->user()->karyawan?->nama ?? auth()->user()->username;
@endphp

{{-- Greeting --}}
<div style="margin-bottom:20px">
    <div style="font-size:22px;font-weight:800;color:#1e293b">
        Selamat Datang, <span style="color:#2563eb">{{ $namaUser }}</span>!
    </div>
</div>

{{-- Stat Cards Admin --}}
<div style="margin-bottom:8px">
    <div style="font-size:15px;font-weight:700;color:#1e293b">
        <i class="ti ti-layout-dashboard" style="color:#2563eb"></i> Ringkasan
    </div>
    <div style="font-size:12px;color:#64748b;margin-top:2px">Informasi data karyawan dan periode</div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
    <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-check"></i> Karyawan Tetap
        </div>
        <div style="font-size:32px;font-weight:800;line-height:1">{{ $karyawanTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan aktif</div>
    </div>
    <div style="background:linear-gradient(135deg,#4f46e5,#4338ca);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-plus"></i> Karyawan Tidak Tetap
        </div>
        <div style="font-size:32px;font-weight:800;line-height:1">{{ $karyawanTidakTetap }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan aktif</div>
    </div>
    <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-off"></i> Tidak Aktif
        </div>
        <div style="font-size:32px;font-weight:800;line-height:1">{{ $totalTidakAktif }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan</div>
    </div>
</div>

{{-- Section Nilai Saya --}}
@if($karyawan)
@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp
<div style="margin-bottom:8px">
    <div style="font-size:15px;font-weight:700;color:#1e293b">
        <i class="ti ti-chart-bar" style="color:#2563eb"></i> Penilaian Saya
    </div>
    <div style="font-size:12px;color:#64748b;margin-top:2px">Hasil penilaian Anda sebagai karyawan</div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:12px">
    <div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:12px;padding:16px 18px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            <i class="ti ti-trophy"></i> Ranking Terakhir
        </div>
        <div style="font-size:28px;font-weight:800;line-height:1">
            {{ $nilaiTerakhir ? '#'.$nilaiTerakhir->ranking : '—' }}
        </div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">
            {{ $nilaiTerakhir ? ($namaBulan[$nilaiTerakhir->periode->bulan] ?? '').' '.$nilaiTerakhir->periode->tahun : 'belum ada penilaian' }}
        </div>
    </div>
    <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;padding:16px 18px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            <i class="ti ti-star"></i> Nilai Kinerja Terakhir
        </div>
        <div style="font-size:24px;font-weight:800;line-height:1">
            {{ $nilaiTerakhir ? number_format($nilaiTerakhir->nilai_preferensi, 3) : '—' }}
        </div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">Nilai Kinerja</div>
    </div>
    <div style="background:linear-gradient(135deg,#0891b2,#0e7490);border-radius:12px;padding:16px 18px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            <i class="ti ti-chart-bar"></i> Total Dinilai
        </div>
        <div style="font-size:28px;font-weight:800;line-height:1">{{ $totalDinilaiSaya }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">periode</div>
    </div>
</div>

@if($nilaiTerakhir)
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-calendar-check"></i> Penilaian Terakhir Saya</span>
        <span style="font-size:11px;color:#64748b">
            {{ ($namaBulan[$nilaiTerakhir->periode->bulan] ?? $nilaiTerakhir->periode->bulan).' '.$nilaiTerakhir->periode->tahun }}
        </span>
    </div>
    <div style="padding:14px 16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:48px;height:48px;border-radius:50%;background:#f0fdf4;border:2px solid #86efac;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="font-size:16px;font-weight:800;color:#16a34a">#{{ $nilaiTerakhir->ranking }}</span>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#1e293b">{{ $karyawan->nama }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">
                    Nilai Kinerja: <strong style="color:#7c3aed">{{ number_format($nilaiTerakhir->nilai_preferensi, 3) }}</strong>
                </div>
            </div>
        </div>
        <a href="{{ route('karyawan.nilai') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-chart-bar"></i> Lihat Detail Nilai
        </a>
    </div>
</div>
@endif
@endif

@endsection