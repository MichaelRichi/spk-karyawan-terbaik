@extends('layouts.app')
@section('title','Atur Bobot — '.$periode->bulan.'/'.$periode->tahun)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('periode.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <h6 class="mb-0 fw-semibold">Atur Bobot — {{ $periode->bulan }}/{{ $periode->tahun }}</h6>
</div>
@php $total = $periodeKriteria->sum('bobot'); @endphp
<div class="alert alert-{{ $total==100?'success':'warning' }} small py-2">
    Total bobot saat ini: <strong>{{ $total }}%</strong>
    @if($total != 100) — harus tepat 100% @endif
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="{{ route('periode.bobot.update',$periode) }}">
            @csrf @method('PUT')
            @foreach($periodeKriteria as $pk)
            <div class="mb-3">
                <label class="form-label">
                    {{ $pk->nama_kriteria }}
                    <span class="badge bg-{{ $pk->jenis=='benefit'?'success':'danger' }} ms-1">{{ $pk->jenis }}</span>
                </label>
                <div class="input-group">
                    <input type="number" name="bobot[{{ $pk->id }}]" class="form-control"
                        value="{{ old('bobot.'.$pk->id, $pk->bobot) }}" min="0" max="100" step="0.01" required>
                    <span class="input-group-text">%</span>
                </div>
            </div>
            @endforeach
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Simpan Bobot</button>
                @if($total == 100)
                <form method="POST" action="{{ route('periode.aktifkan',$periode) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirm('Aktifkan periode ini?')">Aktifkan Periode</button>
                </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection