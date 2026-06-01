@extends('layouts.app')
@section('title', 'Import Data Absen')

@section('content')

<div class="card" style="margin-bottom:16px;max-width:1000px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Import Data Absen</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Upload file Excel untuk mengimpor data kehadiran karyawan — pilih bulan &amp; tahun sendiri</div>
    </div>
</div>

{{-- PETUNJUK --}}
<div class="card" style="max-width:1000px;margin-left:auto;margin-right:auto">
    <div class="card-header">
        <span><i class="ti ti-info-circle"></i> Petunjuk Import Data</span>
    </div>
    <div class="card-body p-3">
        <div class="alert-spk al-info" style="align-items:flex-start">
            <i class="ti ti-list-check"></i>
            <div style="line-height:1.8;font-size:12px;white-space:nowrap">
                <div>• File harus dalam format Excel <strong>.xlsx</strong>.</div>
                <div>• Baris pertama berisi header: <code style="background:rgba(0,0,0,.06);padding:1px 5px;border-radius:4px">NO | NAMA | 1 | 2 | 3 | … | 31</code></div>
                <div>• Isi sel <strong>1</strong> = hadir, sel <strong>kosong</strong> = tidak hadir.</div>
                <div>• Nama sheet = nama bulan: <strong>JAN, FEB, MAR, APR, MEI, JUN, JUL, AGU, SEP, OKT, NOV, DES</strong>. Sistem otomatis memilih sheet sesuai bulan yang dipilih.</div>
                <div>• Nama karyawan di Excel harus cocok dengan data karyawan di sistem.</div>
                <div>• Maksimal ukuran file <strong>10 MB</strong>.</div>
            </div>
        </div>
    </div>
</div>

{{-- FORM IMPORT --}}
<div class="card" style="max-width:1000px;margin-left:auto;margin-right:auto">
    <div class="card-header">
        <span><i class="ti ti-table-import"></i> Form Import Data</span>
    </div>
    <div class="card-body p-4">

        <form method="POST" action="{{ route('absensi.upload.proses-index') }}" enctype="multipart/form-data">
            @csrf

            @php
                $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $thnSkrg   = (int) now()->year;
                $blnDefault= old('bulan', now()->month);
                $thnDefault= old('tahun', $thnSkrg);
            @endphp

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Bulan <span style="color:#ef4444">*</span></label>
                    <div style="position:relative">
                        <select name="bulan" class="form-select @error('bulan') is-invalid @enderror"
                                style="appearance:none;-webkit-appearance:none;padding-right:30px;cursor:pointer">
                            @for($b = 1; $b <= 12; $b++)
                                <option value="{{ $b }}" @selected((int)$blnDefault === $b)>{{ $namaBulan[$b] }}</option>
                            @endfor
                        </select>
                        <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:14px"></i>
                    </div>
                    @error('bulan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pilih Tahun <span style="color:#ef4444">*</span></label>
                    <div style="position:relative">
                        <select name="tahun" class="form-select @error('tahun') is-invalid @enderror"
                                style="appearance:none;-webkit-appearance:none;padding-right:30px;cursor:pointer">
                            @for($t = $thnSkrg + 1; $t >= $thnSkrg - 5; $t--)
                                <option value="{{ $t }}" @selected((int)$thnDefault === $t)>{{ $t }}</option>
                            @endfor
                        </select>
                        <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:14px"></i>
                    </div>
                    @error('tahun')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">File Excel <span style="color:#ef4444">*</span></label>
                <input type="file" name="file_absensi" accept=".xlsx"
                       class="form-control @error('file_absensi') is-invalid @enderror">
                @error('file_absensi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div style="font-size:10px;color:#94a3b8;margin-top:4px">Format yang didukung: .xlsx (Maksimal 10 MB)</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-upload"></i> Import Data Absen
                </button>
                <a href="{{ route('absensi.rekap') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
                    <i class="ti ti-x"></i> Batal
                </a>
            </div>
        </form>

    </div>
</div>

@endsection