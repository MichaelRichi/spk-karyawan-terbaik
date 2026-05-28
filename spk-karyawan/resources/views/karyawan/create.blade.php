@extends('layouts.app')
@section('title','Tambah Karyawan')
@section('content')

<div class="ph">
    <div>
        <div class="ph-title">Tambah Karyawan</div>
        <div class="ph-sub">Isi data karyawan baru</div>
    </div>
    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:560px;margin:0 auto">
<div class="card">
    <div class="card-header"><i class="ti ti-user-plus"></i> Data Karyawan</div>
    <div style="padding:20px">
        <form method="POST" action="{{ route('karyawan.store') }}">
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

            <div style="display:flex;gap:8px">
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary" style="flex:1;justify-content:center">Batal</a>
                <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center">
                    <i class="ti ti-device-floppy"></i> Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection