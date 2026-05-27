@extends('layouts.app')
@section('title','Hasil Ranking — '.(['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun)
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Hasil Ranking — {{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun }}</div>
        <div class="ph-sub">Perhitungan SAW selesai · Periode dikunci</div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('ranking.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('ranking.cetak', $periode) }}" class="btn btn-info-soft" target="_blank">
            <i class="ti ti-file-text"></i> Cetak PDF
        </a>
    </div>
</div>

<div class="steps">
    <div class="step done"><i class="ti ti-check"></i> Buat Periode</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-check"></i> Atur Bobot</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-check"></i> Input Penilaian</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-check"></i> Hitung Penilaian</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-lock"></i> Selesai</div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-lbl">Karyawan Dinilai</div>
        <div class="stat-val">{{ count($detail) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Nilai Kinerja Tertinggi</div>
        <div class="stat-val" style="color:#185FA5">{{ number_format(collect($detail)->max('nilai_preferensi'), 3) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Nilai Kinerja Terendah</div>
        <div class="stat-val" style="color:#64748b;font-size:16px">{{ number_format(collect($detail)->min('nilai_preferensi'), 3) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Karyawan Terbaik</div>
        <div class="stat-val" style="font-size:14px;margin-top:3px">{{ collect($detail)->where('ranking',1)->first()['karyawan']->nama ?? '—' }}</div>
    </div>
</div>

<!-- PODIUM -->
@php $top3 = collect($detail)->take(3); @endphp
<div class="podium">
    @if(isset($top3[0]))
    <div class="pod g1">
        <div class="pod-ico">🥇</div>
        <div class="pod-nm">{{ $top3[0]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Kinerja: {{ number_format($top3[0]['nilai_preferensi'], 3) }}</div>
    </div>
    @else <div></div>
    @endif
    @if(isset($top3[1]))
    <div class="pod g2">
        <div class="pod-ico">🥈</div>
        <div class="pod-nm">{{ $top3[1]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Kinerja: {{ number_format($top3[1]['nilai_preferensi'], 3) }}</div>
    </div>
    @else <div></div>
    @endif
    @if(isset($top3[2]))
    <div class="pod g3">
        <div class="pod-ico">🥉</div>
        <div class="pod-nm">{{ $top3[2]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Kinerja: {{ number_format($top3[2]['nilai_preferensi'], 3) }}</div>
    </div>
    @else <div></div>
    @endif
</div>

{{-- 4 Tabel Accordion SAW --}}

@php
    // Tabel 1-3: urutan nama (belum diranking)
    $detailUnsorted = collect($detail)->sortBy(fn($d) => $d['karyawan']->nama)->values();
@endphp

{{-- Step 1: Nilai Mentah --}}
<div class="card" style="margin-bottom:10px">
    <button type="button" onclick="toggleStep('step1')" style="width:100%;background:none;border:none;cursor:pointer;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;text-align:left">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="width:24px;height:24px;border-radius:50%;background:#2563eb;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">1</span>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:13px">Nilai Mentah (Skor)</div>
                <div style="font-size:10px;color:#64748b">Skor yang diberikan untuk setiap karyawan per kriteria</div>
            </div>
        </div>
        <i class="ti ti-chevron-down" id="chevron-step1" style="color:#64748b;transition:transform .2s"></i>
    </button>
    <div id="step1" style="display:none;border-top:0.5px solid #e2e8f0">
        <div style="overflow-x:auto">
            <table class="table mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        @foreach($periodeKriteria as $pk)
                        <th class="text-center" style="font-size:11px">
                            {{ $pk->nama_kriteria }}<br>
                            <span style="font-size:9px;color:#64748b;font-weight:400">({{ ucfirst($pk->jenis) }})</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailUnsorted as $d)
                    <tr>
                        <td style="font-weight:600">{{ $d['karyawan']->nama }}</td>
                        @foreach($d['detail_kriteria'] as $dk)
                        <td class="text-center" style="font-weight:700;color:#1e293b">{{ $dk['nilai'] }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:8px 14px;background:#f8fafc;font-size:10px;color:#64748b;border-top:0.5px solid #e2e8f0">
            Skor berkisar 1–5. Benefit = skor tinggi lebih baik. Cost = skor rendah lebih baik.
        </div>
    </div>
</div>

{{-- Step 2: Normalisasi --}}
<div class="card" style="margin-bottom:10px">
    <button type="button" onclick="toggleStep('step2')" style="width:100%;background:none;border:none;cursor:pointer;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;text-align:left">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="width:24px;height:24px;border-radius:50%;background:#0891b2;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">2</span>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:13px">Matriks Normalisasi (R)</div>
                <div style="font-size:10px;color:#64748b">Benefit: r = x / max(x) &nbsp;|&nbsp; Cost: r = min(x) / x</div>
            </div>
        </div>
        <i class="ti ti-chevron-down" id="chevron-step2" style="color:#64748b;transition:transform .2s"></i>
    </button>
    <div id="step2" style="display:none;border-top:0.5px solid #e2e8f0">
        <div style="overflow-x:auto">
            <table class="table mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        @foreach($periodeKriteria as $pk)
                        <th class="text-center" style="font-size:11px">{{ $pk->nama_kriteria }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailUnsorted as $d)
                    <tr>
                        <td style="font-weight:600">{{ $d['karyawan']->nama }}</td>
                        @foreach($d['detail_kriteria'] as $dk)
                        <td class="text-center" style="color:#0891b2;font-weight:600">{{ number_format($dk['nilai_normalisasi'], 3) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:8px 14px;background:#f8fafc;font-size:10px;color:#64748b;border-top:0.5px solid #e2e8f0">
            Normalisasi menghasilkan nilai 0–1. Nilai 1.000 = terbaik di kriteria tersebut.
        </div>
    </div>
</div>

{{-- Step 3: Nilai Terbobot --}}
<div class="card" style="margin-bottom:10px">
    <button type="button" onclick="toggleStep('step3')" style="width:100%;background:none;border:none;cursor:pointer;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;text-align:left">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="width:24px;height:24px;border-radius:50%;background:#16a34a;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">3</span>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:13px">Nilai Terbobot (V = R × W)</div>
                <div style="font-size:10px;color:#64748b">Normalisasi dikali bobot kriteria</div>
            </div>
        </div>
        <i class="ti ti-chevron-down" id="chevron-step3" style="color:#64748b;transition:transform .2s"></i>
    </button>
    <div id="step3" style="display:none;border-top:0.5px solid #e2e8f0">
        <div style="overflow-x:auto">
            <table class="table mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        @foreach($periodeKriteria as $pk)
                        <th class="text-center" style="font-size:11px">
                            {{ $pk->nama_kriteria }}<br>
                            <span style="font-size:9px;color:#64748b;font-weight:400">(W={{ $pk->bobot }}%)</span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailUnsorted as $d)
                    <tr>
                        <td style="font-weight:600">{{ $d['karyawan']->nama }}</td>
                        @foreach($d['detail_kriteria'] as $dk)
                        <td class="text-center" style="color:#16a34a;font-weight:600">{{ number_format($dk['nilai_terbobot'], 3) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:8px 14px;background:#f8fafc;font-size:10px;color:#64748b;border-top:0.5px solid #e2e8f0">
            V = R × W. Nilai ini merupakan kontribusi setiap kriteria terhadap nilai akhir.
        </div>
    </div>
</div>

{{-- Step 4: Nilai Preferensi & Ranking (default terbuka) --}}
<div class="card" style="margin-bottom:10px">
    <button type="button" onclick="toggleStep('step4')" style="width:100%;background:none;border:none;cursor:pointer;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;text-align:left">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="width:24px;height:24px;border-radius:50%;background:#7c3aed;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">4</span>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:13px">Nilai Preferensi & Ranking (Vi)</div>
                <div style="font-size:10px;color:#64748b">Vi = Σ(R × W) — jumlah semua nilai terbobot</div>
            </div>
        </div>
        <i class="ti ti-chevron-up" id="chevron-step4" style="color:#64748b;transition:transform .2s"></i>
    </button>
    <div id="step4" style="border-top:0.5px solid #e2e8f0">
        <div style="overflow-x:auto">
            <table class="table mb-0" style="min-width:600px">
                <thead>
                    <tr>
                        <th style="width:60px" class="text-center">Rank</th>
                        <th>Karyawan</th>
                        @foreach($periodeKriteria as $pk)
                        <th class="text-center" style="font-size:11px">{{ $pk->nama_kriteria }}</th>
                        @endforeach
                        <th class="text-center vi-c">Nilai Kinerja (Vi)</th>
                        <th class="text-center" style="width:80px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail as $d)
                    <tr style="{{ $d['ranking']==1?'background:#FAEEDA30':'' }}">
                        <td class="text-center">
                            @if($d['ranking']==1) <span class="rb rb1">1</span>
                            @elseif($d['ranking']==2) <span class="rb rb2">2</span>
                            @elseif($d['ranking']==3) <span class="rb rb3">3</span>
                            @else <span class="rb rbo">{{ $d['ranking'] }}</span>
                            @endif
                        </td>
                        <td style="font-weight:600;color:{{ $d['ranking']==1?'#633806':'' }}">{{ $d['karyawan']->nama }}</td>
                        @foreach($d['detail_kriteria'] as $dk)
                        <td class="text-center"><span class="vw">{{ number_format($dk['nilai_terbobot'], 3) }}</span></td>
                        @endforeach
                        <td class="text-center vi-c">{{ number_format($d['nilai_preferensi'], 3) }}</td>
                        <td class="text-center">
                            <a href="{{ route('ranking.edit-nilai', [$periode, $d['karyawan']->id]) }}"
                                class="btn btn-sm" style="background:#f59e0b;border-color:#f59e0b;color:#fff;font-size:10px">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:8px 14px;background:#f8fafc;font-size:10px;color:#64748b;border-top:0.5px solid #e2e8f0">
            Karyawan dengan Vi tertinggi = ranking terbaik.
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStep(id) {
    const el = document.getElementById(id);
    const ch = document.getElementById('chevron-' + id);
    const open = el.style.display !== 'none';
    el.style.display = open ? 'none' : 'block';
    ch.className = open ? 'ti ti-chevron-down' : 'ti ti-chevron-up';
    ch.style.transition = 'transform .2s';
}
</script>
@endpush
@endsection