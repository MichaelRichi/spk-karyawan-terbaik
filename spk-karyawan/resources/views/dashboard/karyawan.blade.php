@extends('layouts.app')
@section('title','Dashboard')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="ph">
    <div>
        <div class="ph-title">Dashboard</div>
        <div class="ph-sub">Selamat datang, {{ $karyawan?->nama ?? auth()->user()->username }}</div>
    </div>
</div>

@if(!$karyawan)
<div class="card" style="text-align:center;padding:32px">
    <i class="ti ti-user-off" style="font-size:36px;color:#cbd5e1"></i>
    <div style="color:#64748b;margin-top:8px">Akun Anda belum terhubung ke data karyawan.</div>
</div>
@else

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:12px">
    <div class="stat-card" style="{{ $nilaiTerakhir ? 'border-color:#86efac;background:#f0fdf4' : '' }}">
        <div class="stat-lbl" style="{{ $nilaiTerakhir ? 'color:#16a34a' : '' }}">
            <i class="ti ti-trophy"></i> Ranking Terakhir
        </div>
        <div class="stat-val" style="font-size:28px;{{ $nilaiTerakhir ? 'color:#16a34a' : 'color:#94a3b8' }}">
            {{ $nilaiTerakhir ? '#'.$nilaiTerakhir->ranking : '—' }}
        </div>
    </div>
    <div class="stat-card" style="{{ $nilaiTerakhir ? 'border-color:#c4b5fd;background:#f5f3ff' : '' }}">
        <div class="stat-lbl" style="{{ $nilaiTerakhir ? 'color:#7c3aed' : '' }}">
            <i class="ti ti-star"></i> Nilai Akhir Terakhir
        </div>
        <div class="stat-val" style="font-size:22px;{{ $nilaiTerakhir ? 'color:#7c3aed' : 'color:#94a3b8' }}">
            {{ $nilaiTerakhir ? number_format($nilaiTerakhir->nilai_preferensi,4) : '—' }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-chart-bar"></i> Total Dinilai</div>
        <div class="stat-val" style="font-size:28px">{{ $totalDinilai }} <span style="font-size:13px;color:#94a3b8">periode</span></div>
    </div>
</div>

{{-- Info periode terakhir --}}
@if($nilaiTerakhir)
<div class="card" style="margin-bottom:12px">
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
                    Nilai Akhir: <strong style="color:#7c3aed">{{ number_format($nilaiTerakhir->nilai_preferensi,4) }}</strong>
                </div>
            </div>
        </div>
        <a href="{{ route('karyawan.nilai') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-chart-bar"></i> Lihat Detail Nilai
        </a>
    </div>
</div>
@else
<div class="card" style="text-align:center;padding:32px">
    <i class="ti ti-chart-bar" style="font-size:36px;color:#cbd5e1"></i>
    <div style="color:#64748b;margin-top:8px;font-size:13px">Anda belum pernah dinilai.</div>
</div>
@endif

@endif
@endsection