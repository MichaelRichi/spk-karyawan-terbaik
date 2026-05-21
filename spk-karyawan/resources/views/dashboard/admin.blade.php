@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="ph">
    <div>
        <div class="ph-title">Dashboard</div>
        <div class="ph-sub">Selamat datang, {{ auth()->user()->username }}</div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:12px">
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-users"></i> Karyawan Aktif</div>
        <div class="stat-val" style="font-size:28px">{{ $totalKaryawan }}</div>
    </div>
    <div class="stat-card" style="border-color:#fca5a5;background:#fef2f2">
        <div class="stat-lbl" style="color:#dc2626"><i class="ti ti-user-off"></i> Tidak Aktif</div>
        <div class="stat-val" style="font-size:28px;color:#dc2626">{{ $totalTidakAktif }}</div>
    </div>
    <div class="stat-card" style="{{ $periodeAktif ? 'border-color:#93c5fd;background:#eff6ff' : '' }}">
        <div class="stat-lbl" style="{{ $periodeAktif ? 'color:#1d4ed8' : '' }}">
            <i class="ti ti-calendar"></i> Periode Aktif
        </div>
        <div class="stat-val" style="font-size:14px;margin-top:4px;{{ $periodeAktif ? 'color:#1d4ed8' : 'color:#94a3b8' }}">
            @if($periodeAktif)
            @php $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
            {{ ($nb[$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun }}
            @else
            Tidak ada
            @endif
        </div>
    </div>
</div>

{{-- Periode Aktif Progress --}}
@if($periodeAktif)
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-clipboard-check"></i> Progress Penilaian Periode Aktif</span>
    </div>
    <div style="padding:16px">
        @php
            $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $labelBulan = ($nb[$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun;
            $persen = $totalKaryawan > 0 ? round($totalDinilai / $totalKaryawan * 100) : 0;
        @endphp
        <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px">
            <span style="font-weight:600;color:#1e293b">{{ $labelBulan }}</span>
            <span style="font-weight:700;color:{{ $persen==100?'#16a34a':'#f59e0b' }}">{{ $totalDinilai }} / {{ $totalKaryawan }} karyawan</span>
        </div>
        <div style="height:12px;background:#e2e8f0;border-radius:6px;overflow:hidden;margin-bottom:8px">
            <div style="height:100%;width:{{ $persen }}%;background:{{ $persen==100?'#16a34a':'#2563eb' }};border-radius:6px;transition:width .3s"></div>
        </div>
        @if($persen == 100)
        <div class="alert-spk al-ok" style="margin:0">
            <i class="ti ti-check-circle"></i>
            <span>Semua karyawan sudah dinilai. Menunggu perhitungan SAW oleh Direktur.</span>
        </div>
        @else
        <div class="alert-spk al-warn" style="margin:0">
            <i class="ti ti-alert-triangle"></i>
            <span>{{ $totalKaryawan - $totalDinilai }} karyawan belum dinilai.</span>
        </div>
        @endif
    </div>
</div>
@else
<div class="card" style="text-align:center;padding:32px">
    <i class="ti ti-calendar-off" style="font-size:36px;color:#cbd5e1"></i>
    <div style="color:#64748b;margin-top:8px;font-size:13px">Tidak ada periode penilaian yang sedang berjalan.</div>
</div>
@endif

@endsection