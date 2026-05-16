@extends('layouts.app')
@section('title','Buat Periode Baru')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Buat Periode Baru</div>
        <div class="ph-sub">Periode baru akan otomatis menyalin snapshot kriteria & bobot</div>
    </div>
    <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:420px">
    <div class="alert-spk al-info">
        <i class="ti ti-info-circle"></i>
        Saat disimpan, sistem otomatis menyalin snapshot kriteria & bobot. Bobot dapat disesuaikan khusus untuk periode ini setelah dibuat.
    </div>
    <div class="card">
        <div class="card-header"><i class="ti ti-calendar-plus"></i> Detail Periode</div>
        <div style="padding:16px">
            <form method="POST" action="{{ route('periode.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Bulan <span style="color:#ef4444">*</span></label>
                    <select name="bulan" class="form-select @error('bulan') is-invalid @enderror" required>
                        <option value="">-- Pilih bulan --</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                        <option value="{{ $i+1 }}" {{ old('bulan')==$i+1?'selected':'' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                    @error('bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tahun <span style="color:#ef4444">*</span></label>
                    <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                        value="{{ old('tahun', date('Y')) }}" min="2020" max="2099" required>
                    @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan & Atur Bobot</button>
                    <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection