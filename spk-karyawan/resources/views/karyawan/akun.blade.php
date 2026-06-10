@extends('layouts.app')
@section('title', ($user ? 'Edit' : 'Buat').' Akun — '.$karyawan->nama)
@section('content')

<style>
.akun-form .form-label{font-size:14px}
.akun-form .form-control,.akun-form .form-select{font-size:14px;padding:9px 12px}
</style>

<div class="card" style="margin-bottom:16px;max-width:680px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">{{ $user ? 'Edit' : 'Buat' }} Akun</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $user ? 'Perbarui informasi akun pengguna' : 'Buat akun login untuk karyawan ini' }}</div>
        </div>
        <a href="{{ route('karyawan.index') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="max-width:680px;margin:0 auto">

{{-- Info Karyawan --}}
<div class="card" style="margin-bottom:12px">
    <div class="card-header" style="justify-content:flex-start"><i class="ti ti-id-badge-2"></i> Informasi Karyawan</div>
    <div style="padding:14px 16px;display:flex;gap:20px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-user" style="color:#1d4ed8;font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#64748b">Nama</div>
                <div style="font-weight:600;color:#1e293b">{{ $karyawan->nama }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:{{ $karyawan->isAktif()?'#dcfce7':'#fee2e2' }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-circle-check" style="color:{{ $karyawan->isAktif()?'#16a34a':'#dc2626' }};font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#64748b">Status</div>
                <div style="font-weight:600;color:{{ $karyawan->isAktif()?'#16a34a':'#dc2626' }}">
                    {{ $karyawan->isAktif() ? 'Aktif' : 'Tidak Aktif' }}
                </div>
            </div>
        </div>
        @if($user)
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-user-circle" style="color:#7c3aed;font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#64748b">Username saat ini</div>
                <div style="font-weight:600;color:#7c3aed">{{ $user->username }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Form Akun --}}
<div class="card">
    <div class="card-header" style="justify-content:flex-start">
        <i class="ti ti-user-cog"></i> {{ $user ? 'Edit Akun' : 'Buat Akun Baru' }}
    </div>
    <div style="padding:16px">
        @if($errors->any())
        <div class="alert-spk al-warn" style="margin-bottom:14px">
            <i class="ti ti-alert-circle"></i>
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $e)
                <li style="font-size:12px">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" class="akun-form"
            action="{{ $user ? route('karyawan.akun.update', $karyawan) : route('karyawan.akun.store', $karyawan) }}">
            @csrf
            @if($user) @method('PUT') @endif

            <div class="row g-3 mb-3">
                {{-- Username --}}
                <div class="col-md-6">
                    <label class="form-label">Username <span style="color:#ef4444">*</span></label>
                    <input type="text" name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user?->username) }}"
                        placeholder="Username untuk login" required autocomplete="off">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Role --}}
                <div class="col-md-6">
                    <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                    <div class="select-wrap">
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="karyawan" {{ old('role', $user?->role ?? 'karyawan')=='karyawan'?'selected':'' }}>Karyawan</option>
                            <option value="admin"    {{ old('role', $user?->role)=='admin'   ?'selected':'' }}>Admin</option>
                        </select>
                    </div>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Password --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Password <span style="color:#ef4444">{{ $user ? '' : '*' }}</span>
                        @if($user)
                        <span style="color:#94a3b8;font-size:10px">(kosongkan jika tidak ingin mengganti)</span>
                        @endif
                    </label>
                    <div style="position:relative">
                        <input type="password" name="password" id="inp-password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ $user ? 'Password baru (opsional)' : 'Minimal 6 karakter' }}"
                            {{ $user ? '' : 'required' }} autocomplete="new-password">
                        <button type="button" onclick="togglePwd('inp-password','eye-pwd')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#64748b">
                            <i class="ti ti-eye" id="eye-pwd"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <div style="position:relative">
                        <input type="password" name="password_confirmation" id="inp-confirm"
                            class="form-control"
                            placeholder="Ulangi password" autocomplete="new-password">
                        <button type="button" onclick="togglePwd('inp-confirm','eye-confirm')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#64748b">
                            <i class="ti ti-eye" id="eye-confirm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:14px;padding:10px">
                    <i class="ti ti-{{ $user ? 'device-floppy' : 'user-plus' }}"></i>
                    {{ $user ? 'Perbarui Akun' : 'Buat Akun' }}
                </button>
            </div>
        </form>
    </div>
</div>

</div>

@push('scripts')
<style>
.select-wrap{position:relative}
.select-wrap::after{content:'';position:absolute;right:12px;top:50%;transform:translateY(-50%);width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid #64748b;pointer-events:none}
.select-wrap select{appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer}
</style>
<script>
function togglePwd(inputId, eyeId) {
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.className = 'ti ti-eye-off';
    } else {
        inp.type = 'password';
        eye.className = 'ti ti-eye';
    }
}
</script>
@endpush
@endsection