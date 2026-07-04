@extends('layouts.app')
@section('title','Nilai Saya')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;gap:14px">
        <div style="width:46px;height:46px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="ti ti-chart-bar" style="font-size:23px;color:#2563eb"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Nilai Saya</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Riwayat penilaian kinerja Anda per periode</div>
        </div>
    </div>
</div>

@if($riwayat->isEmpty())
<div class="card" style="text-align:center;padding:40px">
    <i class="ti ti-chart-bar" style="font-size:40px;color:#cbd5e1"></i>
    <div style="color:#64748b;margin-top:10px">Belum ada data penilaian.</div>
</div>
@else

{{-- Dropdown pilih periode --}}
<div class="card" style="margin-bottom:12px">
    <div style="padding:14px 16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <label style="font-weight:600;color:#374151;white-space:nowrap">
            <i class="ti ti-calendar"></i> Pilih Periode:
        </label>
        <div style="position:relative;flex:1;min-width:200px;max-width:320px">
            <select id="pilih-periode" class="form-select" onchange="tampilPeriode(this.value)"
                style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer;font-weight:700;color:#1e293b;font-size:15px">
                @foreach($riwayat as $r)
                <option value="{{ $r->id }}" {{ $loop->first ? 'selected' : '' }}>
                    {{ ($namaBulan[$r->periode->bulan] ?? $r->periode->bulan).' '.$r->periode->tahun }}
                </option>
                @endforeach
            </select>
            <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#1e293b;font-size:16px"></i>
        </div>
    </div>
</div>

{{-- Panel per periode --}}
@foreach($riwayat as $r)
@php
    $penilaian = \App\Models\Penilaian::where('karyawan_id', $karyawan->id)
        ->whereHas('periodeKriteria', fn($q) => $q->where('periode_id', $r->periode_id))
        ->with(['periodeSubKriteria', 'periodeKriteria'])
        ->get()
        ->keyBy('periode_kriteria_id');

    $skorNorm = [];
    foreach($r->periode->periodeKriteria as $pk) {
        $p = $penilaian[$pk->id] ?? null;
        if ($p) {
            $allSkor = \App\Models\Penilaian::where('periode_kriteria_id', $pk->id)
                ->with('periodeSubKriteria')->get()->pluck('periodeSubKriteria.skor');
            $skor = $p->periodeSubKriteria->skor;
            $norm = $pk->jenis === 'benefit'
                ? ($allSkor->max() > 0 ? $skor / $allSkor->max() : 0)
                : ($skor > 0 ? $allSkor->min() / $skor : 0);
            $skorNorm[$pk->id] = ['nama' => $pk->nama_kriteria, 'norm' => $norm];
        }
    }

    $top3Periode = $periodeSelesai->where('id', $r->periode_id)->first();
    $top3 = $top3Periode ? $top3Periode->hasilRanking->where('tipe', $karyawan->tipe)->where('ranking', '<=', 3)->sortBy('ranking') : collect();
    $nilaiku = $top3Periode ? $top3Periode->hasilRanking->where('karyawan_id', $karyawan->id)->first() : null;
@endphp

<div id="periode-{{ $r->id }}" class="periode-panel" style="{{ $loop->first ? '' : 'display:none' }}">

    {{-- ① NILAI SENDIRI --}}
    {{-- Stat cards --}}
    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:12px">
        <div class="stat-card" style="border-color:#93c5fd;background:#eff6ff">
            <div class="stat-lbl" style="color:#1d4ed8"><i class="ti ti-calendar"></i> Periode</div>
            <div class="stat-val" style="font-size:15px;margin-top:4px;color:#1d4ed8">
                {{ ($namaBulan[$r->periode->bulan] ?? $r->periode->bulan).' '.$r->periode->tahun }}
            </div>
        </div>
        <div class="stat-card" style="border-color:#86efac;background:#f0fdf4">
            <div class="stat-lbl" style="color:#16a34a"><i class="ti ti-trophy"></i> Ranking</div>
            <div class="stat-val" style="font-size:28px;color:#16a34a">#{{ $r->ranking }}</div>
        </div>
        <div class="stat-card" style="border-color:#c4b5fd;background:#f5f3ff">
            <div class="stat-lbl" style="color:#7c3aed"><i class="ti ti-star"></i> Nilai Akhir</div>
            <div class="stat-val" style="font-size:24px;color:#7c3aed">{{ number_format($r->nilai_preferensi, 3) }}</div>
        </div>
    </div>

    {{-- Kartu per kriteria --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:16px">
        @foreach($r->periode->periodeKriteria->where('tipe', $karyawan->tipe) as $i => $pk)
        @php
            $p = $penilaian[$pk->id] ?? null;
            $skor = $p?->periodeSubKriteria?->skor ?? 0;
            $label = $p?->periodeSubKriteria?->label ?? '—';
            $norm = $skorNorm[$pk->id]['norm'] ?? 0;
            $persen = round($norm * 100);
            $warna = $persen >= 80 ? '#16a34a' : ($persen >= 50 ? '#f59e0b' : '#ef4444');
            $warnaBorder = $persen >= 80 ? '#86efac' : ($persen >= 50 ? '#fcd34d' : '#fca5a5');
        @endphp
        <div style="background:#fff;border:1.5px solid {{ $warnaBorder }};border-radius:12px;overflow:hidden">
            <div style="background:{{ $persen>=80?'#f0fdf4':($persen>=50?'#fffbeb':'#fef2f2') }};padding:12px 16px;border-bottom:1px solid {{ $warnaBorder }}">
                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:4px">{{ $pk->nama_kriteria }}</div>
                <div style="display:flex;align-items:baseline;gap:4px">
                    <span style="font-size:32px;font-weight:800;color:#1e293b;line-height:1">{{ $skor }}</span>
                    <span style="font-size:13px;color:#94a3b8">/ 5</span>
                </div>
            </div>
            <div style="padding:10px 16px">
                <div style="height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;margin-bottom:4px">
                    <div style="height:100%;width:{{ $persen }}%;background:{{ $warna }};border-radius:4px"></div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:11px;font-weight:600;color:#374151">{{ $label }}</span>
                    <span style="font-size:10px;font-weight:700;color:{{ $warna }}">{{ $persen }}%</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ② TOP 3 — accordion/dropdown --}}
    @if($top3->isNotEmpty())
    <div class="card">
        <button type="button" onclick="toggleTop3('top3-{{ $r->id }}')"
            style="width:100%;background:none;border:none;cursor:pointer;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;text-align:left">
            <span style="font-weight:600;color:#1e293b;font-size:13px">
                <i class="ti ti-medal" style="color:#d97706;margin-right:6px"></i>
                Top 3 Karyawan {{ $karyawan->tipe_label }} Terbaik Periode Ini
            </span>
            <i class="ti ti-chevron-down" id="chevron-{{ $r->id }}" style="color:#64748b;transition:transform .2s"></i>
        </button>
        <div id="top3-{{ $r->id }}" style="display:none;border-top:0.5px solid #e2e8f0">
            <div style="padding:14px 16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                @foreach($top3 as $t)
                @php
                    $isMe = $karyawan->id === $t->karyawan_id;
                    $medal = $t->ranking == 1
                        ? ['bg'=>'#fef9e7','border'=>'#f59e0b','icon'=>'ti-medal','ic'=>'#d97706','rc'=>'#d97706']
                        : ($t->ranking == 2
                        ? ['bg'=>'#f8fafc','border'=>'#94a3b8','icon'=>'ti-medal-2','ic'=>'#64748b','rc'=>'#64748b']
                        : ['bg'=>'#fdf8f0','border'=>'#c2855a','icon'=>'ti-medal-3','ic'=>'#b45309','rc'=>'#b45309']);
                @endphp
                <div style="background:{{ $medal['bg'] }};border:{{ $isMe?'2.5px':'1.5px' }} solid {{ $isMe?'#2563eb':$medal['border'] }};border-radius:10px;padding:14px;text-align:center;position:relative">
                    @if($isMe)
                    <div style="position:absolute;top:-8px;left:50%;transform:translateX(-50%);background:#2563eb;color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:10px;white-space:nowrap">Anda</div>
                    @endif
                    <i class="ti {{ $medal['icon'] }}" style="font-size:26px;color:{{ $medal['ic'] }}"></i>
                    <div style="font-size:10px;font-weight:600;color:{{ $medal['rc'] }};margin:4px 0 2px">Ranking #{{ $t->ranking }}</div>
                    <div style="font-size:13px;font-weight:700;color:#1e293b">{{ $t->karyawan?->nama ?? '—' }}</div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;font-weight:600">{{ number_format($t->nilai_preferensi, 3) }}</div>
                </div>
                @endforeach
            </div>
            @if($nilaiku && $nilaiku->ranking > 3)
            <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0;display:flex;align-items:center;gap:10px">
                <div style="width:32px;height:32px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#1d4ed8;flex-shrink:0">#{{ $nilaiku->ranking }}</div>
                <span style="font-size:12px;color:#64748b">
                    Posisi Anda dari <strong>{{ $top3Periode->hasilRanking->where('tipe', $karyawan->tipe)->count() }}</strong> karyawan
                    &nbsp;·&nbsp; Nilai Akhir: <strong style="color:#7c3aed">{{ number_format($nilaiku->nilai_preferensi, 3) }}</strong>
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endforeach
@endif

@push('scripts')
<script>
function tampilPeriode(id) {
    document.querySelectorAll('.periode-panel').forEach(p => p.style.display = 'none');
    document.getElementById('periode-' + id).style.display = 'block';
}
function toggleTop3(id) {
    const el = document.getElementById(id);
    const rid = id.replace('top3-','');
    const chevron = document.getElementById('chevron-' + rid);
    const isHidden = el.style.display === 'none';
    el.style.display = isHidden ? 'block' : 'none';
    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}
</script>
@endpush
@endsection