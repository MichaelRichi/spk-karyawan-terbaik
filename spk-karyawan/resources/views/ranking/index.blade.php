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
    @php $tahunSekarang = null; @endphp
    @forelse($periode as $p)
    @php
        $terbaikTetap = $p->hasilRanking->where('tipe','tetap')->where('ranking',1)->first();
        $terbaikTT    = $p->hasilRanking->where('tipe','tidak_tetap')->where('ranking',1)->first();
    @endphp
    @if($tahunSekarang !== $p->tahun)
    @php $tahunSekarang = $p->tahun; @endphp
    <div class="col-12" style="margin-top:6px">
        <div style="font-weight:800;color:#334155;font-size:13px;letter-spacing:.5px;border-bottom:2px solid #e2e8f0;padding-bottom:6px">
            <i class="ti ti-calendar-stats"></i> Tahun {{ $p->tahun }}
        </div>
    </div>
    @endif
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <span style="font-size:15px;font-weight:700">{{ ((['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$p->bulan]) ?? $p->bulan).' '.$p->tahun }}</span>
                <span class="badge bg-success-soft" style="font-size:12px;padding:4px 11px">Selesai</span>
            </div>
            <div style="padding:12px 14px">
                <div style="background:#eff6ff;border-left:3px solid #2563eb;border-radius:8px;padding:8px 12px;margin-bottom:6px;display:flex;justify-content:space-between;align-items:center;gap:8px">
                    <div style="min-width:0">
                        <div style="font-size:9px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.6px">Karyawan Tetap</div>
                        <div style="font-weight:700;font-size:13px;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">🏆 {{ $terbaikTetap?->karyawan?->nama ?? '—' }}</div>
                    </div>
                    <span style="background:#dbeafe;color:#185FA5;font-weight:700;font-size:12px;padding:3px 10px;border-radius:20px;white-space:nowrap">{{ $terbaikTetap ? number_format($terbaikTetap->nilai_preferensi, 3) : '—' }}</span>
                </div>
                <div style="background:#f0fdfa;border-left:3px solid #0d9488;border-radius:8px;padding:8px 12px;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;gap:8px">
                    <div style="min-width:0">
                        <div style="font-size:9px;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:.6px">Karyawan Tidak Tetap</div>
                        <div style="font-weight:700;font-size:13px;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">🏆 {{ $terbaikTT?->karyawan?->nama ?? '—' }}</div>
                    </div>
                    <span style="background:#ccfbf1;color:#0f766e;font-weight:700;font-size:12px;padding:3px 10px;border-radius:20px;white-space:nowrap">{{ $terbaikTT ? number_format($terbaikTT->nilai_preferensi, 3) : '—' }}</span>
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