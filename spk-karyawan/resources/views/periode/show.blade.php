@extends('layouts.app')
@section('title','Detail Periode — '.$periode->bulan.'/'.$periode->tahun)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('periode.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <h6 class="mb-0 fw-semibold">{{ $periode->bulan }}/{{ $periode->tahun }}</h6>
    <span class="badge bg-{{ $periode->status=='selesai'?'success':($periode->status=='aktif'?'primary':'secondary') }}">
        {{ $periode->status }}
    </span>
</div>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">Kriteria & Bobot Periode Ini</div>
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr><th>Kriteria</th><th>Jenis</th><th class="text-end">Bobot</th></tr>
        </thead>
        <tbody>
            @foreach($periode->periodeKriteria as $pk)
            <tr>
                <td>{{ $pk->nama_kriteria }}</td>
                <td><span class="badge bg-{{ $pk->jenis=='benefit'?'success':'danger' }}">{{ $pk->jenis }}</span></td>
                <td class="text-end fw-semibold">{{ $pk->bobot }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="d-flex gap-2">
    @if($periode->status == 'aktif')
    <a href="{{ route('penilaian.index',$periode) }}" class="btn btn-primary btn-sm">Input Penilaian</a>
    @endif
    @if($periode->status == 'selesai')
    <a href="{{ route('ranking.hasil',$periode) }}" class="btn btn-success btn-sm">Lihat Hasil Ranking</a>
    @endif
</div>
@endsection