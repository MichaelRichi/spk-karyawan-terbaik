@extends('layouts.app')
@section('title','Profil Saya')
@section('content')

@php
$nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$namaDisplay = $karyawan?->nama ?? $user->username;
$inisial = strtoupper(substr($namaDisplay, 0, 2));
$roleColor = $user->role === 'direktur' ? '#1d4ed8' : ($user->role === 'admin' ? '#374151' : '#16a34a');
$roleBg    = $user->role === 'direktur' ? '#dbeafe' : ($user->role === 'admin' ? '#f3f4f6' : '#dcfce7');
@endphp

<div class="card" style="margin:0 auto 16px;max-width:600px">
    <div style="padding:18px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:14px">
            <div style="width:46px;height:46px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-user-circle" style="font-size:24px;color:#2563eb"></i>
            </div>
            <div>
                <div style="font-size:18px;font-weight:800;color:#1e293b">Profil Saya</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Informasi akun dan data diri</div>
            </div>
        </div>
        <a href="{{ route('profil.edit') }}" class="btn btn-primary">
            <i class="ti ti-pencil"></i> Edit Profil
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-spk al-ok" style="margin-bottom:14px">
    <i class="ti ti-check"></i> {{ session('success') }}
</div>
@endif

<div style="max-width:600px;margin:0 auto">

    {{-- Avatar & Nama --}}
    <div class="card" style="margin-bottom:14px">
        <div style="padding:24px;display:flex;align-items:center;gap:20px">
            <div style="width:72px;height:72px;border-radius:50%;background:{{ $roleBg }};display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:{{ $roleColor }};flex-shrink:0;border:3px solid {{ $roleColor }}20">
                {{ $inisial }}
            </div>
            <div>
                <div style="font-size:20px;font-weight:800;color:#1e293b">{{ $namaDisplay }}</div>
                <div style="margin-top:4px">
                    <span style="background:{{ $roleBg }};color:{{ $roleColor }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <div style="font-size:12px;color:#94a3b8;margin-top:6px">
                    <i class="ti ti-at" style="font-size:11px"></i> {{ $user->username }}
                </div>
            </div>
        </div>
    </div>

    {{-- Info Akun --}}
    <div class="card" style="margin-bottom:14px">
        <div class="card-header"><i class="ti ti-shield-check"></i> Informasi Akun</div>
        <div style="padding:16px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Username</div>
                    <div style="font-weight:600;color:#1e293b">{{ $user->username }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Role</div>
                    <div style="font-weight:600;color:{{ $roleColor }}">{{ ucfirst($user->role) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Karyawan --}}
    @if($karyawan)
    <div class="card">
        <div class="card-header"><i class="ti ti-user"></i> Data Karyawan</div>
        <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Nama Lengkap</div>
                    <div style="font-weight:600;color:#1e293b">{{ $karyawan->nama }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Jenis Kelamin</div>
                    <div style="font-weight:600;color:#1e293b">{{ $karyawan->jenis_kelamin }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Tanggal Lahir</div>
                    <div style="font-weight:600;color:#1e293b">{{ $karyawan->tgl_lahir ? $karyawan->tgl_lahir->format('d/m/Y') : '—' }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Tanggal Masuk</div>
                    <div style="font-weight:600;color:#1e293b">{{ $karyawan->tgl_masuk ? $karyawan->tgl_masuk->format('d/m/Y') : '—' }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">No. Telepon</div>
                    <div style="font-weight:600;color:#1e293b">{{ $karyawan->no_telepon ?: '—' }}</div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:8px">
                    <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Status</div>
                    <div>
                        <span class="badge {{ $karyawan->status === 'aktif' ? 'bg-success-soft' : 'bg-danger-soft' }}">
                            {{ ucfirst($karyawan->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @if($karyawan->alamat)
            <div style="padding:12px;background:#f8fafc;border-radius:8px">
                <div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Alamat</div>
                <div style="font-weight:600;color:#1e293b">{{ $karyawan->alamat }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection