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

{{-- Stat Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
    <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-users"></i> Karyawan Aktif
        </div>
        <div style="font-size:32px;font-weight:800;line-height:1">{{ $totalKaryawan }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan</div>
    </div>
    <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-user-off"></i> Tidak Aktif
        </div>
        <div style="font-size:32px;font-weight:800;line-height:1">{{ $totalTidakAktif }}</div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">karyawan</div>
    </div>
    <div style="background:{{ $periodeAktif ? 'linear-gradient(135deg,#0891b2,#0e7490)' : 'linear-gradient(135deg,#64748b,#475569)' }};border-radius:12px;padding:18px 20px;color:#fff">
        <div style="font-size:11px;font-weight:600;opacity:.8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            <i class="ti ti-calendar"></i> Periode Aktif
        </div>
        <div style="font-size:18px;font-weight:800;line-height:1.2">
            {{ $periodeAktif ? ($nb[$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun : 'Tidak Ada' }}
        </div>
        <div style="font-size:11px;opacity:.7;margin-top:4px">{{ $periodeAktif ? 'sedang berjalan' : 'tidak ada periode aktif' }}</div>
    </div>
</div>

{{-- Progress Penilaian --}}
@if($periodeAktif)
@php
    $labelBulan = ($nb[$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun;
    $persen = $totalKaryawan > 0 ? round($totalDinilai / $totalKaryawan * 100) : 0;
@endphp
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-clipboard-check"></i> Progress Penilaian — {{ $labelBulan }}</span>
        <span style="font-size:12px;font-weight:700;color:{{ $persen==100?'#16a34a':'#f59e0b' }}">
            {{ $totalDinilai }} / {{ $totalKaryawan }} karyawan
        </span>
    </div>
    <div style="padding:16px">
        <div style="height:12px;background:#e2e8f0;border-radius:6px;overflow:hidden;margin-bottom:10px">
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