@extends('layouts.app')
@section('title','Edit Karyawan')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('karyawan.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="ti ti-arrow-left"></i>
    </a>
    <h6 class="mb-0 fw-semibold">Edit Karyawan — {{ $karyawan->nama }}</h6>
</div>

<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="{{ route('karyawan.update', $karyawan) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $karyawan->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                    value="{{ old('jabatan', $karyawan->jabatan) }}" required>
                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                    <option value="laki-laki" {{ old('jenis_kelamin',$karyawan->jenis_kelamin)=='laki-laki'?'selected':'' }}>Laki-laki</option>
                    <option value="perempuan" {{ old('jenis_kelamin',$karyawan->jenis_kelamin)=='perempuan'?'selected':'' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                    value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk->format('Y-m-d')) }}" required>
                @error('tanggal_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="tetap" {{ old('status',$karyawan->status)=='tetap'?'selected':'' }}>Tetap</option>
                    <option value="tidak_tetap" {{ old('status',$karyawan->status)=='tidak_tetap'?'selected':'' }}>Tidak Tetap</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti ti-device-floppy me-1"></i> Perbarui
                </button>
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection