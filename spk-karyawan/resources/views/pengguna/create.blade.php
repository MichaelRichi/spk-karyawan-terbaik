@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('content')

<style>
.pg-form .form-label{font-weight:700;color:#1e293b;font-size:13px;margin-bottom:5px}
.pg-form .card-header{justify-content:flex-start;gap:8px;font-weight:700}
</style>

<div class="card" style="margin-bottom:16px;max-width:440px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Tambah Pengguna</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Buat akun baru untuk mengakses sistem</div>
        </div>
        <a href="{{ route('pengguna.index') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card pg-form" style="max-width:440px;margin-left:auto;margin-right:auto">
    <div class="card-header" style="justify-content:flex-start"><i class="ti ti-user-plus"></i> Form Pengguna Baru</div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username <span style="color:#ef4444">*</span></label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username') }}" placeholder="Masukkan username" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password <span style="color:#ef4444">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Minimal 6 karakter" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                <select name="role" class="form-select @error('role') is-invalid @enderror" required
                    onchange="toggleKaryawan(this.value)"
                    style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                    <option value="">-- Pilih role --</option>
                    @if(auth()->user() && auth()->user()->role === 'direktur')
                    <option value="direktur" {{ old('role')=='direktur'?'selected':'' }}>Direktur</option>
                    @endif
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                    <option value="karyawan" {{ old('role')=='karyawan'?'selected':'' }}>Karyawan</option>
                </select>
                <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                </div>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3" id="field-karyawan" style="display:none">
                <label class="form-label">Data Karyawan <span style="color:#ef4444">*</span></label>
                <div style="position:relative">
                <select name="karyawan_id" class="form-select @error('karyawan_id') is-invalid @enderror"
                    style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                    <option value="">-- Pilih karyawan --</option>
                    @foreach($karyawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id')==$k->id?'selected':'' }}>
                        {{ $k->nama }} — {{ $k->jabatan }}
                    </option>
                    @endforeach
                </select>
                <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                </div>
                @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div style="margin-top:4px">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-weight:600;padding:10px"><i class="ti ti-check"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleKaryawan(role) {
    document.getElementById('field-karyawan').style.display = role === 'karyawan' ? 'block' : 'none';
}
toggleKaryawan('{{ old('role') }}');
</script>
@endsection