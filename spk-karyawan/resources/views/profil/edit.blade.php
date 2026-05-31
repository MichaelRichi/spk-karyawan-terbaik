@extends('layouts.app')
@section('title','Edit Profil')
@section('content')

@php
$namaDisplay = $karyawan?->nama ?? $user->username;
$inisial = strtoupper(substr($namaDisplay, 0, 2));
$roleColor = $user->role === 'direktur' ? '#1d4ed8' : ($user->role === 'admin' ? '#374151' : '#16a34a');
$roleBg    = $user->role === 'direktur' ? '#dbeafe' : ($user->role === 'admin' ? '#f3f4f6' : '#dcfce7');
@endphp

<div class="ph">
    <div>
        <div class="ph-title">Edit Profil</div>
        <div class="ph-sub">Perbarui informasi akun Anda</div>
    </div>
    <a href="{{ route('profil.show') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:560px;margin:0 auto">

    {{-- Avatar --}}
    <div style="text-align:center;margin-bottom:20px">
        <div style="width:72px;height:72px;border-radius:50%;background:{{ $roleBg }};display:inline-flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:{{ $roleColor }};border:3px solid {{ $roleColor }}20">
            {{ $inisial }}
        </div>
        <div style="font-size:14px;font-weight:700;color:#1e293b;margin-top:8px">{{ $namaDisplay }}</div>
        <div style="font-size:12px;color:#64748b">{{ ucfirst($user->role) }}</div>
    </div>

    <form method="POST" action="{{ route('profil.update') }}">
        @csrf @method('PUT')

        {{-- Info Akun --}}
        <div class="card" style="margin-bottom:14px">
            <div class="card-header"><i class="ti ti-shield-check"></i> Informasi Akun</div>
            <div style="padding:16px">
                <div class="mb-3">
                    <label class="form-label">Username <span style="color:#ef4444">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user->username) }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="card" style="margin-bottom:14px">
            <div class="card-header"><i class="ti ti-lock"></i> Ganti Password <span style="font-size:11px;font-weight:400;color:#64748b">(kosongkan jika tidak ingin ganti)</span></div>
            <div style="padding:16px">
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <div style="position:relative">
                        <input type="password" name="password_lama" id="pwd-lama"
                            class="form-control @error('password_lama') is-invalid @enderror"
                            placeholder="Masukkan password lama">
                        <button type="button" onclick="togglePwd('pwd-lama','eye-lama')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8">
                            <i class="ti ti-eye" id="eye-lama" style="font-size:16px"></i>
                        </button>
                    </div>
                    @error('password_lama')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div style="position:relative">
                        <input type="password" name="password_baru" id="pwd-baru"
                            class="form-control @error('password_baru') is-invalid @enderror"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePwd('pwd-baru','eye-baru')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8">
                            <i class="ti ti-eye" id="eye-baru" style="font-size:16px"></i>
                        </button>
                    </div>
                    @error('password_baru')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                        class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>
        </div>

        {{-- Data Karyawan (jika terhubung) --}}
        @if($karyawan)
        <div class="card" style="margin-bottom:14px">
            <div class="card-header"><i class="ti ti-user"></i> Data Karyawan</div>
            <div style="padding:16px">
                <div style="background:#f1f5f9;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#64748b">
                    <i class="ti ti-info-circle"></i> Data nama, tanggal lahir, dan jenis kelamin hanya bisa diubah oleh Admin/Direktur.
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror"
                        value="{{ old('no_telepon', $karyawan->no_telepon) }}"
                        placeholder="Contoh: 08123456789">
                    @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" rows="3"
                        class="form-control @error('alamat') is-invalid @enderror"
                        placeholder="Alamat lengkap">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        @endif

        <div style="display:flex;gap:8px">
            <a href="{{ route('profil.show') }}" class="btn btn-outline-secondary" style="flex:1;justify-content:center">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'ti ti-eye-off';
    } else {
        inp.type = 'password';
        icon.className = 'ti ti-eye';
    }
}
</script>
@endpush
@endsection