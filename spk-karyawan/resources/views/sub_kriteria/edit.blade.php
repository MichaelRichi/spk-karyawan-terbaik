@extends('layouts.app')
@section('title','Edit Sub-Kriteria')
@section('content')

<style>
.form-label.fw-600 {
    font-weight:700;
    color:#1e293b;
    font-size:13px;
}
</style>

<div class="card" style="max-width:560px;margin:0 auto 16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Edit Sub-Kriteria</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $kriteria->nama }}</div>
        </div>
        <a href="{{ route('kriteria.sub-kriteria', $kriteria->id) }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">
    <div class="card">
        <div style="padding:20px 22px">
            <form method="POST" action="{{ route('kriteria.sub-kriteria.update', [$kriteria->id, $subKriteria]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-600">Nama</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $subKriteria->nama) }}" placeholder="Contoh: Sangat Baik" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-600">Skor <span style="color:#64748b;font-weight:400">(1–5)</span></label>
                    <input type="number" name="skor" class="form-control @error('skor') is-invalid @enderror"
                        value="{{ old('skor', $subKriteria->skor) }}" min="1" max="5" required>
                    @error('skor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($kriteria->has_rentang)
                <div class="row g-3 mb-3">
                    <div class="col-12" style="margin-bottom:-4px">
                        <div style="font-size:12px;color:#64748b">
                            Rentang nilai {{ $kriteria->satuan_rentang ?? 'angka' }} untuk pengisian otomatis
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-600">Nilai Min</label>
                        <input type="number" name="nilai_min" step="0.01" class="form-control @error('nilai_min') is-invalid @enderror"
                            value="{{ old('nilai_min', $subKriteria->nilai_min) }}" placeholder="0">
                        @error('nilai_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-600">Nilai Max</label>
                        <input type="number" name="nilai_max" step="0.01" class="form-control @error('nilai_max') is-invalid @enderror"
                            value="{{ old('nilai_max', $subKriteria->nilai_max) }}" placeholder="0">
                        @error('nilai_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <label class="form-label fw-600">Keterangan <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
                    <textarea name="keterangan" class="form-control" rows="2"
                        placeholder="Deskripsi singkat untuk pilihan ini">{{ old('keterangan', $subKriteria->keterangan) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection