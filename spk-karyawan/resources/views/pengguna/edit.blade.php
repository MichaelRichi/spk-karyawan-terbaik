@extends('layouts.app')
@section('title','Edit Pengguna — '.$pengguna->username)
@section('content')

<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Edit Pengguna — {{ $pengguna->username }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Perbarui data akun pengguna</div>
        </div>
        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Info Pengguna --}}
<div class="card" style="margin-bottom:12px">
    <div class="card-header"><i class="ti ti-user-circle"></i> Informasi Akun</div>
    <div style="padding:14px 16px;display:flex;gap:20px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-user" style="color:#1d4ed8;font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:10px;color:#64748b">Username</div>
                <div style="font-weight:600;color:#1e293b">{{ $pengguna->username }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-shield" style="color:#7c3aed;font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:10px;color:#64748b">Role saat ini</div>
                <div style="font-weight:600;color:#7c3aed">{{ ucfirst($pengguna->role) }}</div>
            </div>
        </div>
        @if($pengguna->karyawan)
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:40px;height:40px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-id-badge-2" style="color:#16a34a;font-size:18px"></i>
            </div>
            <div>
                <div style="font-size:10px;color:#64748b">Terhubung ke</div>
                <div style="font-weight:600;color:#16a34a">{{ $pengguna->karyawan->nama }} — {{ $pengguna->karyawan->jabatan }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Form Edit --}}
<div class="card">
    <div class="card-header"><i class="ti ti-pencil"></i> Form Edit Pengguna</div>
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

        <form method="POST" action="{{ route('pengguna.update', $pengguna) }}">
            @csrf @method('PUT')

            <div class="row g-3 mb-3">
                {{-- Username --}}
                <div class="col-md-6">
                    <label class="form-label">Username <span style="color:#ef4444">*</span></label>
                    <input type="text" name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $pengguna->username) }}"
                        required autocomplete="off">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Role --}}
                <div class="col-md-6">
                    <label class="form-label">Role <span style="color:#ef4444">*</span></label>
                    <div class="select-wrap">
                        <select name="role" class="form-select @error('role') is-invalid @enderror"
                            required onchange="toggleKaryawan(this.value)">
                            @if(auth()->user() && auth()->user()->role === 'direktur')
                            <option value="direktur" {{ old('role',$pengguna->role)=='direktur'?'selected':'' }}>Direktur</option>
                            @endif
                            <option value="admin"    {{ old('role',$pengguna->role)=='admin'   ?'selected':'' }}>Admin</option>
                            <option value="karyawan" {{ old('role',$pengguna->role)=='karyawan'?'selected':'' }}>Karyawan</option>
                        </select>
                    </div>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Password --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Password Baru
                        <span style="color:#94a3b8;font-size:10px">(kosongkan jika tidak ingin mengganti)</span>
                    </label>
                    <div style="position:relative">
                        <input type="password" name="password" id="inp-password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password baru" autocomplete="new-password">
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
                            placeholder="Ulangi password baru" autocomplete="new-password">
                        <button type="button" onclick="togglePwd('inp-confirm','eye-confirm')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#64748b">
                            <i class="ti ti-eye" id="eye-confirm"></i>
                        </button>
                    </div>
                </div>

                {{-- Data Karyawan --}}
                <div class="col-md-12" id="field-karyawan">
                    <label class="form-label">Data Karyawan</label>
                    <div class="select-wrap">
                        <select name="karyawan_id" class="form-select">
                            <option value="">-- Tidak terhubung --</option>
                            @foreach($karyawan as $k)
                            <option value="{{ $k->id }}" {{ old('karyawan_id',$pengguna->karyawan_id)==$k->id?'selected':'' }}>
                                {{ $k->nama }} — {{ $k->jabatan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i> Perbarui
                </button>
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
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
function toggleKaryawan(role) {
    document.getElementById('field-karyawan').style.display = role === 'karyawan' ? 'block' : 'none';
}
toggleKaryawan('{{ old('role', $pengguna->role) }}');
</script>
@endpush
@endsection