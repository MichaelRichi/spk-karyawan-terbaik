@extends('layouts.app')
@section('title','Dashboard')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$namaUser = auth()->user()->karyawan?->nama ?? auth()->user()->username;
@endphp

{{-- Greeting --}}
<div style="margin-bottom:20px">
    <div style="font-size:22px;font-weight:800;color:#1e293b">
        Selamat Datang, <span style="color:#2563eb">{{ $namaUser }}</span>!
    </div>

</div>

@if(!$karyawan)
<div class="card" style="text-align:center;padding:32px">
    <i class="ti ti-user-off" style="font-size:36px;color:#cbd5e1"></i>
    <div style="color:#64748b;margin-top:8px">Akun Anda belum terhubung ke data karyawan.</div>
</div>
@else

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
    <div style="background:linear-gradient(135deg,#16a34a,#15803d);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-trophy"></i> Ranking Terakhir
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">
            {{ $nilaiTerakhir ? '#'.$nilaiTerakhir->ranking : '—' }}
        </div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">
            {{ $nilaiTerakhir ? ($namaBulan[$nilaiTerakhir->periode->bulan] ?? '').' '.$nilaiTerakhir->periode->tahun : 'belum ada penilaian' }}
        </div>
    </div>
    <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-star"></i> Nilai Akhir Terakhir
        </div>
        <div style="font-size:28px;font-weight:800;line-height:1">
            {{ $nilaiTerakhir ? number_format($nilaiTerakhir->nilai_preferensi, 3) : '—' }}
        </div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">nilai SAW</div>
    </div>
    <div style="background:linear-gradient(135deg,#0891b2,#0e7490);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-chart-bar"></i> Total Dinilai
        </div>
        <div style="font-size:36px;font-weight:800;line-height:1">{{ $totalDinilai }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">periode</div>
    </div>
</div>

{{-- Info penilaian terakhir --}}
@if($nilaiTerakhir)
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-calendar-check"></i> Penilaian Terakhir</span>
        <span style="font-size:11px;color:#64748b">
            {{ ($namaBulan[$nilaiTerakhir->periode->bulan] ?? $nilaiTerakhir->periode->bulan).' '.$nilaiTerakhir->periode->tahun }}
        </span>
    </div>
    <div style="padding:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:14px">
            <div style="width:52px;height:52px;border-radius:50%;background:#f0fdf4;border:2px solid #86efac;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="font-size:18px;font-weight:800;color:#16a34a">#{{ $nilaiTerakhir->ranking }}</span>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:#1e293b">{{ $karyawan->nama }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">
                    Nilai Akhir: <strong style="color:#7c3aed">{{ number_format($nilaiTerakhir->nilai_preferensi, 3) }}</strong>
                </div>
            </div>
        </div>
        <a href="{{ route('karyawan.nilai') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-chart-bar"></i> Lihat Detail
        </a>
    </div>
</div>
@endif

@endif
@endsection