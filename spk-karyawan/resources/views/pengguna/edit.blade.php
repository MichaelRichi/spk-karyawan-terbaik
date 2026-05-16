@extends('layouts.app')
@section('title','Edit Pengguna')
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="ti ti-arrow-left"></i>
    </a>
    <h6 class="mb-0 fw-semibold">Edit Pengguna — {{ $pengguna->username }}</h6>
</div>

<div class="card border-0 shadow-sm" style="max-width:480px">
    <div class="card-body">
        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                    value="{{ old('username', $pengguna->username) }}" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Kosongkan jika tidak ingin mengubah">
                <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required
                    onchange="toggleKaryawan(this.value)">
                    <option value="direktur" {{ old('role',$pengguna->role)=='direktur'?'selected':'' }}>Direktur</option>
                    <option value="admin"    {{ old('role',$pengguna->role)=='admin'?'selected':'' }}>Admin</option>
                    <option value="karyawan" {{ old('role',$pengguna->role)=='karyawan'?'selected':'' }}>Karyawan</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ti ti-device-floppy me-1"></i> Perbarui
                </button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
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