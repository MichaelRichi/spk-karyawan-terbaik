@extends('layouts.app')
@section('title','Hasil Ranking')
@section('content')
<div class="row g-3">
    @forelse($periode as $p)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h6 class="fw-bold mb-0">{{ $p->bulan }}/{{ $p->tahun }}</h6>
                    <span class="badge bg-success">Selesai</span>
                </div>
                @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
                <div class="text-muted small mb-1">Karyawan terbaik:</div>
                <div class="fw-semibold">{{ $terbaik?->karyawan?->nama ?? '—' }}</div>
                <div class="text-primary small">Vi = {{ number_format($terbaik?->nilai_preferensi ?? 0, 4) }}</div>
                <a href="{{ route('ranking.hasil',$p) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">Belum ada hasil ranking.</div>
    @endforelse
</div>
@endsection