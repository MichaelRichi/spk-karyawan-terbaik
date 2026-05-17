@extends('layouts.app')
@section('title','Edit Karyawan')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Edit Karyawan — {{ $karyawan->nama }}</div>
        <div class="ph-sub">Perbarui data karyawan</div>
    </div>
    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:500px">
    <div class="card-header"><i class="ti ti-pencil"></i> Form Edit Karyawan</div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('karyawan.update', $karyawan) }}">
            @csrf @method('PUT')
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $karyawan->nama) }}" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Jabatan <span style="color:#ef4444">*</span></label>
                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                        value="{{ old('jabatan', $karyawan->jabatan) }}" required>
                    @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="laki-laki" {{ old('jenis_kelamin',$karyawan->jenis_kelamin)=='laki-laki'?'selected':'' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin',$karyawan->jenis_kelamin)=='perempuan'?'selected':'' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-control"
                        value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Status <span style="color:#ef4444">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" {{ old('status',$karyawan->status)=='tetap'?'selected':'' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status',$karyawan->status)=='tidak_tetap'?'selected':'' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Perbarui</button>
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection