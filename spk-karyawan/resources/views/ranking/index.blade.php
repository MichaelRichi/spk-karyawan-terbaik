@extends('layouts.app')
@section('title','Hasil Ranking')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Hasil Ranking</div>
        <div class="ph-sub">Rekap hasil perhitungan SAW semua periode</div>
    </div>
</div>

<div class="row g-3">
    @forelse($periode as $p)
    @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <span style="font-weight:600">{{ ((['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$p->bulan]) ?? $p->bulan).' '.$p->tahun }}</span>
                <span class="badge bg-success-soft">Selesai</span>
            </div>
            <div style="padding:12px 14px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <div style="font-size:24px">🏆</div>
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ $terbaik?->karyawan?->nama ?? '—' }}</div>
                        <div style="font-size:11px;color:#185FA5;font-weight:600">
                            Vi = {{ $terbaik ? number_format($terbaik->nilai_preferensi, 4) : '—' }}
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:6px">
                    <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-info-soft btn-sm" style="flex:1;justify-content:center">
                        <i class="ti ti-bar-chart"></i> Lihat Detail
                    </a>
                    <a href="{{ route('ranking.cetak', $p) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i class="ti ti-file-text"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5" style="color:#64748b">
        <i class="ti ti-trophy-off" style="font-size:32px;display:block;margin-bottom:8px"></i>
        Belum ada hasil ranking.
    </div>
    @endforelse
</div>
@endsection