@extends('layouts.app')
@section('title','Hasil Ranking')
@section('content')
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Hasil Ranking</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Rekap Penilaian Karyawan Terbaik Pada Setiap Periode</div>
        </div>
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
                            Nilai Akhir: {{ $terbaik ? number_format($terbaik->nilai_preferensi, 3) : '—' }}
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:6px">
                    @if($p->status === 'selesai' && $p->hasilRanking->isNotEmpty())
                    <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-info-soft btn-sm" style="flex:1;justify-content:center">
                        <i class="ti ti-bar-chart"></i> Lihat Detail
                    </a>
                    <a href="{{ route('ranking.cetak', $p) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i class="ti ti-file-text"></i>
                    </a>
                    @else
                    <span style="font-size:11px;color:#94a3b8;font-style:italic">Belum ada hasil</span>
                    @endif
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