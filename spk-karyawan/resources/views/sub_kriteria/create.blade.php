@extends('layouts.app')
@section('title','Tambah Sub-Kriteria')
@section('content')

<style>
.sk-form .form-label{font-weight:700;color:#1e293b;font-size:13px;margin-bottom:5px}
.sk-form .form-control{font-size:14px;padding:9px 12px}
</style>

<div class="card" style="max-width:560px;margin:0 auto 16px">
    <div style="padding:20px 24px;display:flex;align-items:center;gap:14px">
        <div style="width:46px;height:46px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="ti ti-plus" style="font-size:23px;color:#2563eb"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Tambah Sub-Kriteria</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $kriteria->nama }}</div>
        </div>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">
    <div class="card">
        <div style="padding:20px 22px">
            <form method="POST" action="{{ route('kriteria.sub-kriteria.store', $kriteria) }}" class="sk-form">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Skor <span style="color:#ef4444">*</span></label>
                    <input type="number" name="skor" class="form-control @error('skor') is-invalid @enderror"
                        value="{{ old('skor') }}" min="1" max="10" required>
                    <div style="font-size:11px;color:#64748b;margin-top:3px">Nilai numerik 1–10. Tidak boleh duplikat dalam satu kriteria.</div>
                    @error('skor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama') }}" placeholder="Contoh: ≥ 26 hari, Sangat Baik" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if($kriteria->has_rentang)
                <div class="mb-3">
                    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:6px">
                        Rentang Nilai <span style="font-size:10px;color:#64748b;font-weight:400">(untuk pengisian otomatis)</span>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <div style="flex:1">
                            <label style="font-size:12px;color:#475569;font-weight:600;margin-bottom:3px;display:block">Min ({{ $kriteria->satuan_rentang ?? 'angka' }})</label>
                            <input type="number" name="nilai_min" class="form-control" step="0.01" min="0" placeholder="0" value="{{ old('nilai_min') }}">
                        </div>
                        <div style="color:#94a3b8;margin-top:16px">—</div>
                        <div style="flex:1">
                            <label style="font-size:12px;color:#475569;font-weight:600;margin-bottom:3px;display:block">Max ({{ $kriteria->satuan_rentang ?? 'angka' }})</label>
                            <input type="number" name="nilai_max" class="form-control" step="0.01" min="0" placeholder="99" value="{{ old('nilai_max') }}">
                        </div>
                    </div>
                    <div style="font-size:10px;color:#94a3b8;margin-top:4px">
                        Isi 0 dan 0 untuk "kurang dari 1 tahun"
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <label class="form-label">Keterangan <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
                    <textarea name="keterangan" class="form-control" rows="2"
                        placeholder="Deskripsi singkat untuk pilihan ini">{{ old('keterangan') }}</textarea>
                </div>

                <div style="display:flex;gap:10px">
                    <a href="{{ route('kriteria.sub-kriteria', $kriteria) }}" class="btn" style="flex:1;justify-content:center;font-size:14px;padding:10px;background:#e2e8f0;border:1.5px solid #94a3b8;color:#1e293b;font-weight:700">Batal</a>
                    <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;font-size:14px;padding:10px;font-weight:600">
                        <i class="ti ti-device-floppy"></i> Simpan Sub-Kriteria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection