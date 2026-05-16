@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-primary">{{ $totalKaryawan }}</div>
            <div class="text-muted small">Total Karyawan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-success">{{ $totalPeriode }}</div>
            <div class="text-muted small">Periode Selesai</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-warning">{{ $periodeAktif?->bulan.'/'.$periodeAktif?->tahun ?? '—' }}</div>
            <div class="text-muted small">Periode Aktif</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-3 fw-bold text-danger">{{ $karyawanTerbaik?->karyawan?->nama ?? '—' }}</div>
            <div class="text-muted small">Terbaik Terakhir</div>
        </div>
    </div>
</div>

@if($periodeAktif)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">Periode Aktif — {{ $periodeAktif->bulan }}/{{ $periodeAktif->tahun }}</div>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2 small">
            <span>Karyawan dinilai</span>
            <span class="fw-semibold">{{ $periodeAktif->penilaian->pluck('karyawan_id')->unique()->count() }} / {{ $totalKaryawan }}</span>
        </div>
        <div class="progress" style="height:8px">
            @php $pct = $totalKaryawan > 0 ? ($periodeAktif->penilaian->pluck('karyawan_id')->unique()->count() / $totalKaryawan * 100) : 0; @endphp
            <div class="progress-bar" style="width:{{ $pct }}%"></div>
        </div>
        <div class="mt-3">
            <a href="{{ route('penilaian.index', $periodeAktif) }}" class="btn btn-sm btn-primary">Input Penilaian</a>
        </div>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Riwayat Periode</div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>Periode</th><th>Status</th><th>Karyawan Terbaik</th><th class="text-center">Vi</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($riwayat as $p)
            <tr>
                <td>{{ $p->bulan }}/{{ $p->tahun }}</td>
                <td><span class="badge bg-{{ $p->status=='selesai'?'success':($p->status=='aktif'?'primary':'secondary') }}">{{ $p->status }}</span></td>
                <td>{{ $p->hasilRanking->where('ranking',1)->first()?->karyawan?->nama ?? '—' }}</td>
                <td class="text-center fw-semibold text-primary">{{ number_format($p->hasilRanking->where('ranking',1)->first()?->nilai_preferensi ?? 0, 4) }}</td>
                <td class="text-center">
                    @if($p->status=='selesai')
                    <a href="{{ route('ranking.hasil',$p) }}" class="btn btn-sm btn-outline-primary">Hasil</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada periode.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection