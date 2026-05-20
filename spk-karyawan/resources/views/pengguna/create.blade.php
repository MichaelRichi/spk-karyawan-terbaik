@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Tambah Pengguna</div>
        <div class="ph-sub">Buat akun baru untuk mengakses sistem</div>
    </div>
    <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:440px">
    <div class="card-header"><i class="ti ti-user-plus"></i> Form Pengguna Baru</div>
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
                <select name="role" class="form-select @error('role') is-invalid @enderror" required
                    onchange="toggleKaryawan(this.value)">
                    <option value="">-- Pilih role --</option>
                    @if(auth()->user() && auth()->user()->role === 'direktur')
                    <option value="direktur" {{ old('role')=='direktur'?'selected':'' }}>Direktur</option>
                    @endif
                    <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                    <option value="karyawan" {{ old('role')=='karyawan'?'selected':'' }}>Karyawan</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3" id="field-karyawan" style="display:none">
                <label class="form-label">Data Karyawan <span style="color:#ef4444">*</span></label>
                <select name="karyawan_id" class="form-select @error('karyawan_id') is-invalid @enderror">
                    <option value="">-- Pilih karyawan --</option>
                    @foreach($karyawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id')==$k->id?'selected':'' }}>
                        {{ $k->nama }} — {{ $k->jabatan }}
                    </option>
                    @endforeach
                </select>
                @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Batal</a>
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