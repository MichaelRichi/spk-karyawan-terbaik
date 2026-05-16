@extends('layouts.app')
@section('title','Nilai — '.$karyawan->nama)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('penilaian.index',$periode) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <div>
        <h6 class="mb-0 fw-semibold">{{ $karyawan->nama }}</h6>
        <small class="text-muted">{{ $periode->bulan }}/{{ $periode->tahun }}</small>
    </div>
</div>

<form method="POST" action="{{ route('penilaian.simpan',[$periode,$karyawan]) }}">
    @csrf
    <div class="row g-3">
        @foreach($periode->periodeKriteria as $pk)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <span class="fw-semibold">{{ $pk->nama_kriteria }}</span>
                    <span class="badge bg-{{ $pk->jenis=='benefit'?'success':'danger' }} ms-1">{{ $pk->jenis }}</span>
                    <span class="badge bg-secondary ms-1">{{ $pk->bobot }}%</span>
                </div>
                <div class="card-body">
                    @foreach($pk->periodeSubKriteria->sortByDesc('skor') as $psk)
                    @php $terpilih = isset($nilaiExisting[$pk->id]) && $nilaiExisting[$pk->id]->periode_sub_kriteria_id == $psk->id; @endphp
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio"
                            name="penilaian[{{ $pk->id }}]"
                            id="psk_{{ $psk->id }}"
                            value="{{ $psk->id }}"
                            {{ $terpilih ? 'checked' : '' }} required>
                        <label class="form-check-label" for="psk_{{ $psk->id }}">
                            <span class="badge bg-primary me-1">{{ $psk->skor }}</span>
                            {{ $psk->nama }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Simpan Penilaian
        </button>
        <a href="{{ route('penilaian.index',$periode) }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection