@extends('layouts.app')
@section('title','Buat Periode Baru')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('periode.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <h6 class="mb-0 fw-semibold">Buat Periode Baru</h6>
</div>
<div class="card border-0 shadow-sm" style="max-width:400px">
    <div class="card-body">
        <div class="alert alert-info small py-2">
            Saat disimpan, sistem otomatis menyalin snapshot kriteria & bobot untuk periode ini.
        </div>
        <form method="POST" action="{{ route('periode.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                <select name="bulan" class="form-select @error('bulan') is-invalid @enderror" required>
                    <option value="">-- Pilih bulan --</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                    <option value="{{ $i+1 }}" {{ old('bulan')==$i+1?'selected':'' }}>{{ $bln }}</option>
                    @endforeach
                </select>
                @error('bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                    value="{{ old('tahun', date('Y')) }}" min="2020" max="2099" required>
                @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection