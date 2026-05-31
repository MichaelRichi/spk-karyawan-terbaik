@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')

<style>
/* ── Rekap absensi ── */
.rk-toolbar{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px}
.rk-toolbar .fld{display:flex;flex-direction:column;min-width:200px}
.rk-grid-wrap{overflow-x:auto;border:0.5px solid #e2e8f0;border-radius:8px}
.rk-table{border-collapse:separate;border-spacing:0;width:100%;min-width:max-content}
.rk-table th,.rk-table td{border-bottom:0.5px solid #f1f5f9;border-right:0.5px solid #f1f5f9;text-align:center;white-space:nowrap}
.rk-table thead th{background:#f8fafc;color:#64748b;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;padding:7px 4px;position:sticky;top:0;z-index:3}
.rk-table tbody td{padding:5px 4px;font-size:11px;vertical-align:middle}
.rk-table tbody tr:hover td{background:#f8fafc}
/* kolom No + Nama dibuat sticky di kiri */
.rk-col-no{position:sticky;left:0;z-index:4;background:#fff;min-width:34px;width:34px}
.rk-col-nm{position:sticky;left:34px;z-index:4;background:#fff;text-align:left !important;min-width:150px;padding-left:10px !important}
.rk-table thead .rk-col-no,.rk-table thead .rk-col-nm{z-index:5;background:#f8fafc}
.rk-table tbody tr:hover .rk-col-no,.rk-table tbody tr:hover .rk-col-nm{background:#f8fafc}
.rk-col-nm .nm{font-weight:600;color:#1e293b;font-size:12px}
.rk-day{min-width:30px;width:30px}
.rk-total{position:sticky;right:0;z-index:4;background:#fff;min-width:54px;font-weight:700;color:#0C447C}
.rk-table thead .rk-total{z-index:5;background:#f8fafc}
.rk-table tbody tr:hover .rk-total{background:#f8fafc}
/* badge status di sel */
.cellb{display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:6px;font-size:10px;font-weight:700;line-height:1}
.cellb.h{background:#EAF3DE;color:#27500A}
.cellb.a{background:#FCEBEB;color:#B91C1C}
.cellb.i{background:#FAEEDA;color:#633806}
.cellb.t{background:#FCF2CC;color:#7A5C00}
.cellb.s{background:#E6F1FB;color:#0C447C}
.cellb.x{background:#f8fafc;color:#cbd5e1}
.rk-legend{display:flex;flex-wrap:wrap;gap:14px;font-size:11px;color:#64748b;margin-top:10px}
.rk-legend .lg{display:flex;align-items:center;gap:5px}

@media print{
    .sb,.topbar,.rk-noprint{display:none !important}
    .main{margin-left:0 !important}
    .content{padding:0 !important}
    .rk-grid-wrap{overflow:visible;border:none}
    .card{border:none;box-shadow:none}
    body{background:#fff}
    .rk-table th,.rk-table td{border-color:#cbd5e1 !important}
}
</style>

{{-- Card Judul (gaya sama seperti Kelola Pengguna) --}}
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Rekap Absensi</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Pantau kehadiran karyawan per hari</div>
        </div>
        <a href="{{ route('absensi.upload.index') }}" class="btn btn-primary rk-noprint">
            <i class="ti ti-file-upload"></i> Import Excel
        </a>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-users"></i> Total Karyawan</div>
        <div class="stat-val">{{ $stat['total_karyawan'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-calendar-check"></i> Hari Kerja</div>
        <div class="stat-val">{{ $stat['hari_kerja'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-calendar"></i> Bulan</div>
        <div class="stat-val" style="font-size:15px;padding-top:5px">{{ $labelBulan }}</div>
    </div>
</div>

{{-- FILTER --}}
<div class="card rk-noprint">
    <div class="card-header">
        <span><i class="ti ti-filter"></i> Filter Data Absen</span>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ route('absensi.rekap') }}" class="rk-toolbar">
            @php
                $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $thnSkrg   = (int) now()->year;
            @endphp
            <div class="fld" style="min-width:160px">
                <label class="form-label">Pilih Bulan</label>
                <div style="position:relative">
                    <select name="bulan" class="form-select" style="appearance:none;-webkit-appearance:none;padding-right:30px;cursor:pointer" onchange="this.form.submit()">
                        @for($b = 1; $b <= 12; $b++)
                            <option value="{{ $b }}" @selected((int)$bulan === $b)>{{ $namaBulan[$b] }}</option>
                        @endfor
                    </select>
                    <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:14px"></i>
                </div>
            </div>
            <div class="fld" style="min-width:120px">
                <label class="form-label">Pilih Tahun</label>
                <div style="position:relative">
                    <select name="tahun" class="form-select" style="appearance:none;-webkit-appearance:none;padding-right:30px;cursor:pointer" onchange="this.form.submit()">
                        @for($t = $thnSkrg + 1; $t >= $thnSkrg - 5; $t--)
                            <option value="{{ $t }}" @selected((int)$tahun === $t)>{{ $t }}</option>
                        @endfor
                    </select>
                    <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:14px"></i>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-search"></i> Tampilkan
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="ti ti-printer"></i> Cetak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TABEL GRID --}}
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-table"></i> Tabel Kehadiran Karyawan — {{ $labelBulan }}</span>
    </div>
    <div class="card-body p-2">

        @if(empty($workingDays))
            <div class="alert-spk al-warn" style="margin:10px">
                <i class="ti ti-alert-triangle"></i>
                <div>
                    Belum ada data absensi untuk <strong>{{ $labelBulan }}</strong>.
                    Silakan <a href="{{ route('absensi.upload.index') }}" style="font-weight:600;text-decoration:underline">import file Excel</a> absensi bulan ini.
                </div>
            </div>
        @else
            <div class="rk-grid-wrap">
                <table class="rk-table">
                    <thead>
                        <tr>
                            <th class="rk-col-no">No</th>
                            <th class="rk-col-nm">Nama Karyawan</th>
                            @foreach($workingDays as $d)
                                <th class="rk-day">{{ $d }}</th>
                            @endforeach
                            <th class="rk-total">Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawanList as $i => $k)
                            @php
                                $row   = $grid[$k->id] ?? [];
                                $hadir = collect($row)->filter(fn($c) => ($c['status'] ?? null) === 'hadir')->count();
                            @endphp
                            <tr>
                                <td class="rk-col-no" style="color:#94a3b8">{{ $i + 1 }}</td>
                                <td class="rk-col-nm"><span class="nm">{{ $k->nama }}</span></td>
                                @foreach($workingDays as $d)
                                    @php
                                        $cell = $row[$d] ?? null;
                                        $st   = $cell['status'] ?? null;
                                        $late = $cell['terlambat'] ?? false;
                                        if ($st === 'hadir' && $late) {
                                            $cls = 't'; $lbl = 'T'; $title = 'Hadir (Terlambat)';
                                        } elseif ($st === 'hadir') {
                                            $cls = 'h'; $lbl = 'H'; $title = 'Hadir';
                                        } elseif ($st !== null) {
                                            $cls = 'a'; $lbl = '✕'; $title = 'Tidak Hadir';
                                        } else {
                                            $cls = 'x'; $lbl = '·'; $title = 'Belum ada data';
                                        }
                                    @endphp
                                    <td class="rk-day">
                                        <span class="cellb {{ $cls }}" title="Tgl {{ $d }}: {{ $title }}">{{ $lbl }}</span>
                                    </td>
                                @endforeach
                                <td class="rk-total">{{ $hadir }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($workingDays) + 3 }}" style="padding:16px;color:#94a3b8">
                                    Tidak ada karyawan aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rk-legend">
                <div class="lg"><span class="cellb h">H</span> Hadir</div>
                <div class="lg"><span class="cellb t">T</span> Terlambat</div>
                <div class="lg"><span class="cellb a">✕</span> Tidak Hadir</div>
                <div class="lg"><span class="cellb x">·</span> Belum ada data</div>
            </div>
        @endif

    </div>
</div>

@endsection