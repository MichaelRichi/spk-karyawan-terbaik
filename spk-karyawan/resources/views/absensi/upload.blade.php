@extends('layouts.app')
@section('title', 'Upload Absensi Excel')

@section('content')

@php
    $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="ph">
    <div>
        <div class="ph-title">Upload Absensi — {{ $namaBulan[$periode->bulan] }} {{ $periode->tahun }}</div>
        <div class="ph-sub">Upload file Excel absensi untuk mengisi nilai kehadiran otomatis ke penilaian</div>
    </div>
    <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali ke Penilaian
    </a>
</div>

<div class="card" style="max-width:580px">
    <div class="card-header">
        <span><i class="ti ti-file-spreadsheet"></i> Upload Excel Absensi</span>
        <span class="badge {{ $periode->status === 'aktif' ? 'bg-success-soft' : 'bg-gray-soft' }}">
            {{ $namaBulan[$periode->bulan] }} {{ $periode->tahun }} — {{ ucfirst($periode->status) }}
        </span>
    </div>
    <div class="card-body p-4">

        <div class="alert-spk al-info" style="margin-bottom:16px">
            <i class="ti ti-info-circle"></i>
            <div>
                <div style="font-weight:600;margin-bottom:4px">Format Excel yang diterima</div>
                <div style="font-family:monospace;font-size:10px;background:rgba(0,0,0,.06);padding:5px 8px;border-radius:4px;margin-bottom:6px">
                    NO | NAMA | 1 | 2 | 3 | ... | 31
                </div>
                <div>Nilai <strong>1</strong> = hadir, kosong = tidak hadir/libur.</div>
                <div>Sheet name = nama bulan: <strong>JAN, FEB, MAR, APR, MEI, JUN, JUL, AGU, SEP, OKT, NOV, DES</strong></div>
                <div style="margin-top:2px">Sistem otomatis memilih sheet <strong>{{ strtoupper(substr($namaBulan[$periode->bulan], 0, 3)) }}</strong> sesuai periode ini.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('absensi.upload.proses', $periode) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="form-label">File Excel (.xlsx) <span style="color:#ef4444">*</span></label>
                <input type="file" name="file_absensi" accept=".xlsx"
                       class="form-control @error('file_absensi') is-invalid @enderror">
                @error('file_absensi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div style="font-size:10px;color:#94a3b8;margin-top:4px">Format: .xlsx | Maks 4 MB</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-upload"></i> Proses Upload
                </button>
                <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>

    </div>
</div>

@endsection