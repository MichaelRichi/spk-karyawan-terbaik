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
    <div class="step done"><i class="ti ti-check"></i> Hitung SAW</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-lock"></i> Selesai</div>
</div>

<div class="alert-spk al-ok">
    <i class="ti ti-shield-check"></i>
    Periode dikunci. Data bersifat read-only untuk menjaga integritas hasil historis.
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-lbl">Karyawan Dinilai</div>
        <div class="stat-val">{{ count($detail) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Vi Tertinggi</div>
        <div class="stat-val" style="color:#185FA5">{{ number_format(collect($detail)->max('nilai_preferensi'), 4) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Vi Terendah</div>
        <div class="stat-val" style="color:#64748b;font-size:16px">{{ number_format(collect($detail)->min('nilai_preferensi'), 4) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Karyawan Terbaik</div>
        <div class="stat-val" style="font-size:14px;margin-top:3px">{{ collect($detail)->where('ranking',1)->first()['karyawan']->nama ?? '—' }}</div>
    </div>
</div>

<!-- PODIUM -->
@php $top3 = collect($detail)->take(3); @endphp
<div class="podium">
    @if(isset($top3[1]))
    <div class="pod g2">
        <div class="pod-ico">🥈</div>
        <div class="pod-nm">{{ $top3[1]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Akhir: {{ number_format($top3[1]['nilai_preferensi'],4) }}</div>
    </div>
    @else <div></div>
    @endif
    @if(isset($top3[0]))
    <div class="pod g1">
        <div class="pod-ico">🥇</div>
        <div class="pod-nm">{{ $top3[0]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Akhir: {{ number_format($top3[0]['nilai_preferensi'],4) }}</div>
    </div>
    @else <div></div>
    @endif
    @if(isset($top3[2]))
    <div class="pod g3">
        <div class="pod-ico">🥉</div>
        <div class="pod-nm">{{ $top3[2]['karyawan']->nama }}</div>
        <div class="pod-vi">Nilai Akhir: {{ number_format($top3[2]['nilai_preferensi'],4) }}</div>
    </div>
    @else <div></div>
    @endif
</div>

<!-- Tabel SAW -->
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-table"></i> Tabel Nilai Mentah · Normalisasi · Terbobot · Vi</span>
        <div style="display:flex;gap:10px;font-size:10px;color:#64748b">
            <span style="color:#185FA5">■</span> r=normalisasi
            <span style="color:#27500A">■</span> W×r=terbobot
        </div>
    </div>
    <div style="background:#f8fafc;padding:6px 14px;border-bottom:0.5px solid #e2e8f0;font-size:10px;color:#64748b;font-family:monospace">
        Benefit: r = x / Max(x) &nbsp;|&nbsp; Cost: r = Min(x) / x &nbsp;|&nbsp; Vi = Σ(W × r)
    </div>
    <div style="overflow-x:auto">
        <table class="table mb-0" style="min-width:700px">
            <thead>
                <tr>
                    <th rowspan="2" style="vertical-align:middle;width:5%">Rank</th>
                    <th rowspan="2" style="vertical-align:middle;width:10%">Karyawan</th>
                    @foreach($periode->periodeKriteria as $pk)
                    <th colspan="3" class="text-center" style="border-left:0.5px solid #e2e8f0">
                        {{ $pk->nama_kriteria }} ({{ $pk->bobot }}%)
                    </th>
                    @endforeach
                    <th rowspan="2" class="text-center vi-c" style="vertical-align:middle;width:8%">Vi</th>
                </tr>
                <tr>
                    @foreach($periode->periodeKriteria as $pk)
                    <th class="text-center" style="border-left:0.5px solid #e2e8f0">x</th>
                    <th class="text-center">r</th>
                    <th class="text-center">W×r</th>
                    @endforeach
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
                    <td>
                        <div style="font-weight:600;color:{{ $d['ranking']==1?'#633806':'' }}">{{ $d['karyawan']->nama }}</div>
                        <div style="font-size:9px;color:#64748b">A{{ $d['ranking'] }}</div>
                    </td>
                    @foreach($d['detail_kriteria'] as $dk)
                    <td class="text-center" style="border-left:0.5px solid #f1f5f9">
                        <span style="font-weight:600">{{ $dk['nilai'] }}</span>
                    </td>
                    <td class="text-center"><span class="vr">{{ number_format($dk['nilai_normalisasi'],3) }}</span></td>
                    <td class="text-center"><span class="vw">{{ number_format($dk['nilai_terbobot'],3) }}</span></td>
                    @endforeach
                    <td class="text-center vi-c">{{ number_format($d['nilai_preferensi'],4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="display:flex;gap:14px;padding:8px 14px;border-top:0.5px solid #e2e8f0;flex-wrap:wrap">
        <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:4px">
            <span style="width:8px;height:8px;background:#374151;display:inline-block;border-radius:2px"></span>x = nilai mentah
        </span>
        <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:4px">
            <span style="width:8px;height:8px;background:#185FA5;display:inline-block;border-radius:2px"></span>r = nilai normalisasi
        </span>
        <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:4px">
            <span style="width:8px;height:8px;background:#27500A;display:inline-block;border-radius:2px"></span>W×r = nilai terbobot
        </span>
        <span style="font-size:10px;color:#64748b;display:flex;align-items:center;gap:4px">
            <span style="width:8px;height:8px;background:#0C447C;display:inline-block;border-radius:2px"></span>Vi = Σ(W×r)
        </span>
    </div>
</div>
@endsection