<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') — SPK Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
<style>
body{background:#f4f6f9;font-size:14px}
.sb{width:220px;min-height:100vh;background:#1e293b;position:fixed;top:0;left:0;z-index:100}
.sb .brand{padding:16px;color:#fff;font-weight:600;font-size:15px;border-bottom:1px solid rgba(255,255,255,.1)}
.sb .nav-link{color:#94a3b8;padding:8px 16px;display:flex;align-items:center;gap:8px;font-size:13px;border-radius:0}
.sb .nav-link:hover,.sb .nav-link.on{color:#fff;background:rgba(255,255,255,.08)}
.sb .nav-link i{font-size:16px;flex-shrink:0}
.sb-sec{padding:12px 16px 4px;font-size:10px;color:#475569;text-transform:uppercase;letter-spacing:.08em}
.sb-foot{position:fixed;bottom:0;left:0;width:220px;padding:12px 16px;border-top:1px solid rgba(255,255,255,.1);background:#1e293b}
.main{margin-left:220px;padding:20px}
.topbar{background:#fff;padding:10px 20px;margin:-20px -20px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between}
</style>
</head>
<body>

<div class="sb">
    <div class="brand"><i class="ti ti-bolt me-2"></i>SPK Karyawan</div>
    <div class="mt-1">

        {{-- SEMUA ROLE: Dashboard --}}
        <div class="sb-sec">Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'on' : '' }}">
            <i class="ti ti-speedometer-2"></i> Dashboard
        </a>

        {{-- ADMIN & DIREKTUR: Pengguna, Karyawan --}}
        @if(in_array(auth()->user()->role, ['admin', 'direktur']))
        <div class="sb-sec">Data Master</div>
        <a href="{{ route('pengguna.index') }}" class="nav-link {{ request()->routeIs('pengguna.*') ? 'on' : '' }}">
            <i class="ti ti-users"></i> Pengguna
        </a>
        <a href="{{ route('karyawan.index') }}" class="nav-link {{ request()->routeIs('karyawan.*') ? 'on' : '' }}">
            <i class="ti ti-id-badge-2"></i> Karyawan
        </a>
        @endif

        {{-- DIREKTUR: Kriteria, Periode, Penilaian --}}
        @if(auth()->user()->role === 'direktur')
        <a href="{{ route('kriteria.index') }}" class="nav-link {{ request()->routeIs('kriteria.*') ? 'on' : '' }}">
            <i class="ti ti-adjustments"></i> Kriteria
        </a>

        <div class="sb-sec">Penilaian</div>
        <a href="{{ route('periode.index') }}" class="nav-link {{ request()->routeIs('periode.*') ? 'on' : '' }}">
            <i class="ti ti-calendar"></i> Periode
        </a>
        @endif

        {{-- SEMUA ROLE: Hasil Ranking --}}
        <div class="sb-sec">Laporan</div>
        <a href="{{ route('ranking.index') }}" class="nav-link {{ request()->routeIs('ranking.*') ? 'on' : '' }}">
            <i class="ti ti-trophy"></i> Hasil Ranking
        </a>

        {{-- ADMIN & KARYAWAN: Nilai Saya --}}
        @if(in_array(auth()->user()->role, ['admin', 'karyawan']))
        <a href="{{ route('karyawan.nilai') }}" class="nav-link {{ request()->routeIs('karyawan.nilai') ? 'on' : '' }}">
            <i class="ti ti-chart-bar"></i> Nilai Saya
        </a>
        @endif

    </div>

    <div class="sb-foot">
        <div style="color:#94a3b8;font-size:12px;margin-bottom:6px">
            <i class="ti ti-user me-1"></i>{{ auth()->user()->username }}
            <span class="badge bg-secondary ms-1" style="font-size:10px">{{ auth()->user()->role }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;color:#ef4444;font-size:12px;padding:0;cursor:pointer">
                <i class="ti ti-logout me-1"></i> Keluar
            </button>
        </form>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <div style="font-size:15px;font-weight:600;color:#1e293b">@yield('title')</div>
        <div style="font-size:12px;color:#64748b">PT Cempaka Indah Abadi</div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
        <i class="ti ti-check me-1"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-2 mb-3">
        <i class="ti ti-x-circle me-1"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>