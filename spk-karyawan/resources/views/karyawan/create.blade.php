@extends('layouts.app')
@section('title','Tambah Karyawan')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Tambah Karyawan</div>
        <div class="ph-sub">Data karyawan baru PT Cempaka Indah Abadi</div>
    </div>
    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:500px">
    <div class="card-header"><i class="ti ti-user-plus"></i> Form Karyawan Baru</div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('karyawan.store') }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama') }}" placeholder="Nama karyawan" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Jabatan <span style="color:#ef4444">*</span></label>
                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                        value="{{ old('jabatan') }}" placeholder="Jabatan" required>
                    @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">-- Pilih --</option>
                        <option value="laki-laki" {{ old('jenis_kelamin')=='laki-laki'?'selected':'' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin')=='perempuan'?'selected':'' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                        value="{{ old('tanggal_masuk') }}" required>
                    @error('tanggal_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Status <span style="color:#ef4444">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="tetap" {{ old('status','tetap')=='tetap'?'selected':'' }}>Tetap</option>
                        <option value="tidak_tetap" {{ old('status')=='tidak_tetap'?'selected':'' }}>Tidak Tetap</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection