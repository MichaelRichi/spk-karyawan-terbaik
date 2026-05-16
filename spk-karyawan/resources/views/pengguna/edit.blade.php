@extends('layouts.app')
@section('title','Edit Pengguna')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Edit Pengguna — {{ $pengguna->username }}</div>
        <div class="ph-sub">Perbarui data akun pengguna</div>
    </div>
    <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:440px">
    <div class="card-header"><i class="ti ti-pencil"></i> Form Edit Pengguna</div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Username <span style="color:#ef4444">*</span></label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username', $pengguna->username) }}" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Kosongkan jika tidak ingin mengubah">
                <div style="font-size:10px;color:#64748b;margin-top:3px">Biarkan kosong jika tidak ingin mengubah password.</div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                <select name="role" class="form-select" required onchange="toggleKaryawan(this.value)">
                    <option value="direktur" {{ old('role',$pengguna->role)=='direktur'?'selected':'' }}>Direktur</option>
                    <option value="admin" {{ old('role',$pengguna->role)=='admin'?'selected':'' }}>Admin</option>
                    <option value="karyawan" {{ old('role',$pengguna->role)=='karyawan'?'selected':'' }}>Karyawan</option>
                </select>
            </div>
            <div class="mb-3" id="field-karyawan">
                <label class="form-label">Data Karyawan</label>
                <select name="karyawan_id" class="form-select">
                    <option value="">-- Tidak terhubung --</option>
                    @foreach($karyawan as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id',$pengguna->karyawan_id)==$k->id?'selected':'' }}>
                        {{ $k->nama }} — {{ $k->jabatan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Perbarui</button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<script>
function toggleKaryawan(role) {
    document.getElementById('field-karyawan').style.display = role === 'karyawan' ? 'block' : 'none';
}
toggleKaryawan('{{ old('role', $pengguna->role) }}');
</script>
@endsection