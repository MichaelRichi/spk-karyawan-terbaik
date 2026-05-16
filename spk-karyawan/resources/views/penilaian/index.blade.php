@extends('layouts.app')
@php
    /** @var \App\Models\Periode $periode */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Karyawan[] $karyawan */
    /** @var \Illuminate\Support\Collection $penilaianSelesai */
    /** @var array $nilaiKaryawan */
@endphp
@section('title','Input Penilaian — '.$periode->bulan.'/'.$periode->tahun)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('periode.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <h6 class="mb-0 fw-semibold">Penilaian — {{ $periode->bulan }}/{{ $periode->tahun }}</h6>
    @php $selesaiSemua = $karyawan->every(fn($k) => $penilaianSelesai->contains($k->id)); @endphp
    @if($selesaiSemua && auth()->user()->isDirektur())
    <form method="POST" action="{{ route('ranking.hitung',$periode) }}" class="ms-auto">
        @csrf
        <button class="btn btn-success btn-sm" onclick="return confirm('Jalankan perhitungan SAW?')">
            <i class="ti ti-calculator me-1"></i> Hitung SAW
        </button>
    </form>
    @endif
</div>

@if(!$selesaiSemua)
<div class="alert alert-warning small py-2">
    <i class="ti ti-alert-triangle me-1"></i>
    {{ $karyawan->count() - $penilaianSelesai->count() }} karyawan belum dinilai. Hitung SAW aktif setelah semua selesai.
</div>
@endif

<div class="card border-0 shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr>
                <th>Karyawan</th>
                @foreach($periode->periodeKriteria as $pk)
                <th class="text-center small">{{ $pk->nama_kriteria }}<br><span class="text-muted">{{ $pk->bobot }}%</span></th>
                @endforeach
                <th class="text-center">Status</th>
                @if(auth()->user()->isDirektur())
                <th class="text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($karyawan as $k)
            @php $sudah = $penilaianSelesai->contains($k->id); @endphp
            <tr>
                <td class="fw-semibold">{{ $k->nama }}</td>
                @foreach($periode->periodeKriteria as $pk)
                @php $p = $nilaiKaryawan[$k->id][$pk->id] ?? null; @endphp
                <td class="text-center small">
                    @if($p)
                    <span class="badge bg-primary">{{ $p->periodeSubKriteria?->skor ?? $p->nilai }}</span>
                    <div class="text-muted" style="font-size:10px">{{ $p->periodeSubKriteria?->nama ?? '' }}</div>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                </td>
                @endforeach
                <td class="text-center">
                    <span class="badge bg-{{ $sudah?'success':'warning text-dark' }}">
                        {{ $sudah?'Lengkap':'Belum' }}
                    </span>
                </td>
                @if(auth()->user()->isDirektur())
                <td class="text-center">
                    <a href="{{ route('penilaian.form',[$periode,$k]) }}"
                        class="btn btn-sm {{ $sudah?'btn-outline-primary':'btn-primary' }}">
                        {{ $sudah?'Edit':'Input' }}
                    </a>
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection