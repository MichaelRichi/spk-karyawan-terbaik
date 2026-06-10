@extends('layouts.app')
@section('title','Tambah Karyawan')
@section('content')

<style>
.kform .form-label{font-size:14px;font-weight:700;color:#1e293b;margin-bottom:5px}
.kform .form-control,.kform .form-select,.kform textarea{font-size:14px;padding:9px 12px}
.kform .form-select{padding-right:32px}
.kform .card-header{justify-content:flex-start;gap:8px;font-weight:700}
</style>

<div class="card" style="margin-bottom:16px;max-width:560px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:14px">
            <div style="width:46px;height:46px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-user-plus" style="font-size:23px;color:#2563eb"></i>
            </div>
            <div>
                <div style="font-size:18px;font-weight:800;color:#1e293b">Tambah Karyawan</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Isi data karyawan baru</div>
            </div>
        </div>
        <a href="{{ route('karyawan.index') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">
<div class="card">
    <div class="card-header" style="justify-content:flex-start"><i class="ti ti-user-plus"></i> Data Karyawan</div>
    <div style="padding:20px">
        <form method="POST" action="{{ route('karyawan.store') }}" class="kform">
            @csrf

            {{-- Nama --}}
            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama') }}" placeholder="Nama lengkap karyawan" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Jenis Kelamin & Tanggal Lahir --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                    <div style="position:relative">
                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki"  {{ old('jenis_kelamin')=='Laki-laki' ?'selected':'' }}>Laki-laki</option>
                        <option value="Perempuan"  {{ old('jenis_kelamin')=='Perempuan' ?'selected':'' }}>Perempuan</option>
                    </select>
                    <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                    </div>
                    @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror"
                        value="{{ old('tgl_lahir') }}">
                    @error('tgl_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Tanggal Masuk & Status --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Tanggal Masuk <span style="color:#ef4444">*</span></label>
                    <input type="date" name="tgl_masuk" class="form-control @error('tgl_masuk') is-invalid @enderror"
                        value="{{ old('tgl_masuk') }}" required>
                    @error('tgl_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label class="form-label">Status <span style="color:#ef4444">*</span></label>
                    <div style="position:relative">
                    <select name="status" class="form-select @error('status') is-invalid @enderror" style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer" required>
                        <option value="aktif"       {{ old('status','aktif')=='aktif'       ?'selected':'' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status')=='tidak_aktif'         ?'selected':'' }}>Tidak Aktif</option>
                    </select>
                    <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                    </div>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- No. Telepon --}}
            <div class="mb-3">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                    value="{{ old('no_telepon') }}" placeholder="Contoh: 08123456789">
                @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-4">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror"
                    placeholder="Alamat lengkap karyawan">{{ old('alamat') }}</textarea>
                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="margin-top:4px">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:14px;padding:10px;font-weight:600">
                    <i class="ti ti-device-floppy"></i> Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection