@extends('layouts.app')
@section('title','Absensi Saya')
@section('content')

<style>
.ap-grid{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px}
.ap-day{width:42px;text-align:center;border-radius:8px;padding:5px 3px;font-size:11px}
.ap-day .ap-num{font-size:10px;font-weight:600;margin-bottom:2px}
.ap-day .ap-badge{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:5px;font-size:11px;font-weight:700}
.ap-day.hadir   {background:#dcfce7}.ap-day.hadir .ap-num{color:#15803d}.ap-day.hadir .ap-badge{background:#16a34a;color:#fff}
.ap-day.telat   {background:#fef9c3}.ap-day.telat .ap-num{color:#854d0e}.ap-day.telat .ap-badge{background:#d97706;color:#fff}
.ap-day.absen   {background:#fee2e2}.ap-day.absen .ap-num{color:#991b1b}.ap-day.absen .ap-badge{background:#dc2626;color:#fff}
.ap-stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:16px}
.ap-stat{
    position:relative;
    background:var(--cb,#fff);
    border:1px solid #e9eef5;
    border-radius:14px;
    padding:18px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    overflow:hidden;
    box-shadow:0 1px 3px rgba(15,23,42,.04);
    transition:transform .15s ease, box-shadow .15s ease;
}
.ap-stat::before{
    content:'';
    position:absolute;
    left:0;top:0;bottom:0;
    width:5px;
    background:var(--ac);
}
.ap-stat:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(15,23,42,.08);
}
.ap-stat .lbl{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px}
.ap-stat .val{font-size:28px;font-weight:800;line-height:1}
.ap-stat-ic{
    width:50px;height:50px;
    border-radius:13px;
    background:var(--icbg,var(--ac));
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
.ap-stat-ic i{font-size:25px;color:var(--ac)}
.ap-legend{display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:#64748b;margin-bottom:8px}
.ap-legend span{display:flex;align-items:center;gap:5px}
</style>

{{-- Header --}}
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Absensi Saya</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Rekap kehadiran pribadi per bulan</div>
    </div>
</div>

@if(!$karyawan)
<div class="alert-spk al-warn">
    <i class="ti ti-alert-triangle"></i>
    Akun Anda belum terhubung ke data karyawan. Hubungi direktur atau admin untuk menghubungkan akun.
</div>
@else

{{-- Filter --}}
<div class="card" style="margin-bottom:16px">
    <div style="padding:14px 18px">
        <form id="filter-form" method="GET" action="{{ route('absensi.pribadi') }}" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">

            {{-- Dropdown Bulan --}}
            <div style="position:relative">
                <select name="bulan" id="bulan-select"
                    style="appearance:none;-webkit-appearance:none;padding:7px 36px 7px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;color:#1e293b;background:#fff;cursor:pointer;min-width:110px"
                    onchange="document.getElementById('filter-form').submit()">
                    @foreach($namaBulan as $i => $nb)
                        @if($i > 0)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $nb }}</option>
                        @endif
                    @endforeach
                </select>
                <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;font-size:14px;color:#64748b"></i>
            </div>

            {{-- Stepper Tahun --}}
            <div style="display:flex;align-items:center;gap:0;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff">
                <button type="button" onclick="gantiTahun(-1)"
                    style="width:34px;height:36px;border:none;background:transparent;cursor:pointer;color:#64748b;font-size:16px;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">‹</button>
                <span id="tahun-display" style="min-width:46px;text-align:center;font-size:13px;font-weight:700;color:#1e293b;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;padding:0 4px;line-height:36px">{{ $tahun }}</span>
                <button type="button" onclick="gantiTahun(1)"
                    style="width:34px;height:36px;border:none;background:transparent;cursor:pointer;color:#64748b;font-size:16px;display:flex;align-items:center;justify-content:center"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">›</button>
                <input type="hidden" name="tahun" id="tahun-input" value="{{ $tahun }}">
            </div>

            <span style="font-size:13px;color:#64748b;font-weight:600"></span>
        </form>
    </div>
</div>

@push('scripts')
<script>
function gantiTahun(delta) {
    const input   = document.getElementById('tahun-input');
    const display = document.getElementById('tahun-display');
    let year = parseInt(input.value) + delta;
    if (year < 2000 || year > 2100) return;
    input.value = year;
    display.textContent = year;
    document.getElementById('filter-form').submit();
}
</script>
@endpush

{{-- Stat Cards --}}
<div class="ap-stat-grid">
    <div class="ap-stat" style="--ac:#16a34a;--icbg:#dcfce7">
        <div>
            <div class="lbl">Hari Hadir</div>
            <div class="val" style="color:#16a34a">{{ $stat['total_hadir'] }}</div>
        </div>
        <div class="ap-stat-ic"><i class="ti ti-calendar-check"></i></div>
    </div>
    <div class="ap-stat" style="--ac:#d97706;--icbg:#fef3c7">
        <div>
            <div class="lbl">Terlambat</div>
            <div class="val" style="color:#d97706">{{ $stat['total_terlambat'] }}</div>
        </div>
        <div class="ap-stat-ic"><i class="ti ti-clock-exclamation"></i></div>
    </div>
    <div class="ap-stat" style="--ac:#2563eb;--icbg:#dbeafe">
        <div>
            <div class="lbl">Hari Kerja</div>
            <div class="val" style="color:#2563eb">{{ $stat['hari_kerja'] }}</div>
        </div>
        <div class="ap-stat-ic"><i class="ti ti-calendar"></i></div>
    </div>
</div>

{{-- Card: Keterangan + Grid Kehadiran --}}
<div class="card" style="padding:16px 18px">
    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:12px">
        <i class="ti ti-calendar-stats" style="color:#2563eb"></i> Detail Kehadiran — {{ $labelBulan }}
    </div>

    {{-- Legenda --}}
    <div class="ap-legend" style="margin-bottom:12px">
        <span><span class="ap-badge" style="background:#16a34a;color:#fff;width:22px;height:22px;border-radius:4px;font-size:11px;display:inline-flex;align-items:center;justify-content:center">H</span> Hadir</span>
        <span><span class="ap-badge" style="background:#d97706;color:#fff;width:22px;height:22px;border-radius:4px;font-size:11px;display:inline-flex;align-items:center;justify-content:center">T</span> Terlambat</span>
        <span><span class="ap-badge" style="background:#dc2626;color:#fff;width:22px;height:22px;border-radius:4px;font-size:11px;display:inline-flex;align-items:center;justify-content:center">✕</span> Tidak Hadir</span>
    </div>

    <div style="border-top:1px solid #f1f5f9;margin-bottom:12px"></div>

    {{-- Grid Hari --}}
    @if(empty($workingDays))
    <div style="text-align:center;padding:24px;color:#94a3b8">
        <i class="ti ti-calendar-off" style="font-size:28px;display:block;margin-bottom:6px"></i>
        Belum ada data absensi untuk {{ $labelBulan }}.
    </div>
    @else
    <div class="ap-grid" style="margin-bottom:0">
        @foreach($workingDays as $d)
        @php
            $cell = $grid[$d] ?? null;
            if (!$cell) { continue; }
            elseif ($cell['terlambat']) { $cls='telat'; $label='T'; }
            elseif ($cell['status']==='hadir') { $cls='hadir'; $label='H'; }
            else { $cls='absen'; $label='✕'; }
        @endphp
        <div class="ap-day {{ $cls }}">
            <div class="ap-num">{{ $d }}</div>
            <div class="ap-badge">{{ $label }}</div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endif
@endsection