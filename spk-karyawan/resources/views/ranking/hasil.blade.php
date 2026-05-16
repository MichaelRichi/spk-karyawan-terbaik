@extends('layouts.app')
@section('title','Hasil Ranking — '.$periode->bulan.'/'.$periode->tahun)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('ranking.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <h6 class="mb-0 fw-semibold">Hasil Ranking — {{ $periode->bulan }}/{{ $periode->tahun }}</h6>
    <a href="{{ route('ranking.cetak',$periode) }}" class="btn btn-sm btn-outline-danger ms-auto" target="_blank">
        <i class="ti ti-file-text me-1"></i> Cetak PDF
    </a>
</div>

<!-- Podium Top 3 -->
@php $top3 = collect($detail)->take(3); @endphp
<div class="row g-2 mb-4 justify-content-center">
    @foreach([1,0,2] as $idx)
    @if(isset($top3[$idx]))
    @php $r = $top3[$idx]; @endphp
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3 {{ $r['ranking']==1?'border-warning border':''}}" style="{{ $r['ranking']==1?'background:#fffbeb':'' }}">
            <div class="fs-2">{{ $r['ranking']==1?'🥇':($r['ranking']==2?'🥈':'🥉') }}</div>
            <div class="fw-semibold">{{ $r['karyawan']->nama }}</div>
            <div class="text-primary fw-bold">{{ number_format($r['nilai_preferensi'],4) }}</div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<!-- Tabel detail -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Detail Perhitungan SAW</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle small">
            <thead class="table-light">
                <tr>
                    <th>Rank</th>
                    <th>Karyawan</th>
                    @foreach($periode->periodeKriteria as $pk)
                    <th class="text-center">{{ $pk->nama_kriteria }}<br><span class="text-muted">x / r / W×r</span></th>
                    @endforeach
                    <th class="text-center bg-primary bg-opacity-10">Vi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail as $d)
                <tr {{ $d['ranking']==1?'style=background:#fffbeb':'' }}>
                    <td>
                        @if($d['ranking']==1) 🥇
                        @elseif($d['ranking']==2) 🥈
                        @elseif($d['ranking']==3) 🥉
                        @else #{{ $d['ranking'] }}
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $d['karyawan']->nama }}</td>
                    @foreach($d['detail_kriteria'] as $dk)
                    <td class="text-center">
                        <div>{{ $dk['nilai'] }}</div>
                        <div class="text-primary">{{ number_format($dk['nilai_normalisasi'],3) }}</div>
                        <div class="text-success">{{ number_format($dk['nilai_terbobot'],3) }}</div>
                    </td>
                    @endforeach
                    <td class="text-center fw-bold text-primary bg-primary bg-opacity-10">
                        {{ number_format($d['nilai_preferensi'],4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white small text-muted">
        x = nilai mentah &nbsp;|&nbsp; r = normalisasi &nbsp;|&nbsp; W×r = terbobot &nbsp;|&nbsp; Vi = Σ(W×r)
    </div>
</div>
@endsection