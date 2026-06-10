<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title') — SPK Karyawan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
<style>
*{box-sizing:border-box}
body{background:#f1f5f9;font-size:13px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}

/* SIDEBAR */
.sb{width:220px;min-height:100vh;background:#0f172a;position:fixed;top:0;left:0;z-index:100;display:flex;flex-direction:column}
.sb-top{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06)}
.sb-logo-row{display:flex;align-items:center;gap:10px}
.sb-logo{width:36px;height:36px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sb-logo i{color:#fff;font-size:18px}
.sb-brand strong{display:block;color:#fff;font-size:13px;font-weight:600;line-height:1.3}
.sb-brand span{font-size:10px;color:#475569}
.sb-nav{flex:1;padding:8px 0}
.sb-grp{font-size:9px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#374151;padding:10px 0 3px 16px}
.sbi{display:flex;align-items:center;gap:9px;padding:7px 12px;margin:1px 8px;border-radius:6px;cursor:pointer;color:#94a3b8;text-decoration:none;font-size:12px;font-weight:500;transition:all .15s;position:relative}
.sbi:hover{background:rgba(255,255,255,.07);color:#e2e8f0;text-decoration:none}
.sbi.on{background:rgba(37,99,235,.2);color:#93c5fd}
.sbi.on::before{content:'';position:absolute;left:-8px;top:50%;transform:translateY(-50%);width:3px;height:16px;background:#3b82f6;border-radius:0 2px 2px 0}
.sbi i{font-size:17px;flex-shrink:0;width:20px;text-align:center}
.sb-user{padding:10px 14px;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:8px;position:sticky;bottom:0;background:#0f172a;margin-top:auto;flex-shrink:0}
.sb-av{width:32px;height:32px;border-radius:50%;background:#1e40af;display:flex;align-items:center;justify-content:center;color:#bfdbfe;font-size:11px;font-weight:600;flex-shrink:0}
.sb-un{font-size:11px;font-weight:500;color:#e2e8f0}
.sb-ur{font-size:9px;color:#64748b}

/* MAIN */
.main{margin-left:220px;min-height:100vh;display:flex;flex-direction:column}
.topbar{background:#fff;padding:0 20px;height:56px;border-bottom:0.5px solid #e2e8f0;display:flex;align-items:center;gap:8px;position:sticky;top:0;z-index:50}
.bc{display:flex;align-items:center;gap:4px;font-size:11px;color:#64748b}
.bc .cur{color:#1e293b;font-weight:600}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:8px}
.rpill{font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:#dbeafe;color:#1e40af}
.content{padding:20px;flex:1}

/* ALERTS */
.alert-spk{border-radius:8px;padding:8px 12px;font-size:11px;display:flex;align-items:flex-start;gap:7px;margin-bottom:12px;border-left:3px solid}
.alert-spk i{font-size:14px;flex-shrink:0;margin-top:1px}
.al-success{background:#EAF3DE;border-color:#22c55e;color:#27500A}
.al-danger{background:#FCEBEB;border-color:#ef4444;color:#791F1F}

/* CARDS */
.card{border:0.5px solid #e2e8f0;border-radius:10px;background:#fff;margin-bottom:10px;width:100%}
.card-header{background:#fff;border-bottom:0.5px solid #e2e8f0;padding:10px 14px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:space-between;border-radius:10px 10px 0 0}
.card-header i{font-size:14px;color:#64748b;margin-right:5px}

/* TABLES */
.table th{background:#f8fafc;color:#64748b;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;padding:7px 10px;border-bottom:0.5px solid #e2e8f0;white-space:nowrap}
.table td{padding:8px 10px;border-bottom:0.5px solid #f1f5f9;font-size:12px;vertical-align:middle}
.table tr:last-child td{border-bottom:none}
.table-hover tbody tr:hover td{background:#f8fafc}

/* BUTTONS */
.btn{font-size:11px;font-weight:500;padding:5px 12px;border-radius:6px;display:inline-flex;align-items:center;gap:4px;transition:all .15s}
.btn i{font-size:13px}
.btn-primary{background:#2563eb;border-color:#2563eb;color:#fff}
.btn-primary:hover{background:#1d4ed8;border-color:#1d4ed8;color:#fff}
.btn-outline-primary{border-color:#2563eb;color:#2563eb}
.btn-outline-primary:hover{background:#2563eb;color:#fff}
.btn-outline-secondary{border-color:#e2e8f0;color:#64748b}
.btn-outline-secondary:hover{background:#f8fafc}
.btn-outline-danger{border-color:#fca5a5;color:#dc2626}
.btn-outline-danger:hover{background:#dc2626;color:#fff}
.btn-success-soft{background:#EAF3DE;color:#27500A;border-color:#97C459}
.btn-info-soft{background:#E6F1FB;color:#0C447C;border-color:#85B7EB}

/* BADGES */
.badge{font-size:9px;font-weight:600;padding:2px 7px;border-radius:8px}
.bg-primary{background:#2563eb !important}
.bg-success-soft{background:#EAF3DE;color:#27500A}
.bg-danger-soft{background:#FCEBEB;color:#791F1F}
.bg-warning-soft{background:#FAEEDA;color:#633806}
.bg-info-soft{background:#E6F1FB;color:#0C447C}
.bg-gray-soft{background:#F1EFE8;color:#444441}

/* FORMS */
.form-control,.form-select{font-size:12px;padding:6px 10px;border:0.5px solid #e2e8f0;border-radius:6px;background:#fff}
.form-control:focus,.form-select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.form-label{font-size:11px;font-weight:600;color:#374151;margin-bottom:4px}

/* PAGE HEADER */
.ph{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:10px}
.ph-title{font-size:15px;font-weight:600;color:#0f172a}
.ph-sub{font-size:11px;color:#64748b;margin-top:2px}

/* STEP BAR */
.steps{display:flex;align-items:center;gap:4px;margin-bottom:12px;overflow-x:auto;padding-bottom:2px}
.step{display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;white-space:nowrap;background:#f1f5f9;color:#64748b;border:0.5px solid #e2e8f0;flex-shrink:0}
.step.done{background:#EAF3DE;color:#27500A;border-color:#97C459}
.step.now{background:#E6F1FB;color:#0C447C;border-color:#85B7EB}
.step i{font-size:11px}
.step-arr{color:#cbd5e1;font-size:11px;flex-shrink:0}

/* STAT CARDS */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;margin-bottom:12px;width:100%}
.stat-card{background:#f8fafc;border-radius:8px;padding:10px 12px;border:0.5px solid #e2e8f0}
.stat-lbl{font-size:10px;color:#64748b;margin-bottom:2px}
.stat-val{font-size:20px;font-weight:600;color:#0f172a}

/* PODIUM */
.podium{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px}
.pod{border:0.5px solid #e2e8f0;border-radius:8px;padding:10px;text-align:center}
.pod-ico{font-size:22px;margin-bottom:4px}
.pod-nm{font-size:12px;font-weight:600}
.pod-vi{font-size:10px;margin-top:2px}
.pod.g1{border-color:#BA7517;background:#FAEEDA}.pod.g1 .pod-nm{color:#633806}.pod.g1 .pod-vi{color:#854F0B}
.pod.g2{border-color:#B4B2A9;background:#F1EFE8}.pod.g2 .pod-nm{color:#444441}.pod.g2 .pod-vi{color:#5F5E5A}
.pod.g3{border-color:#97C459;background:#EAF3DE}.pod.g3 .pod-nm{color:#27500A}.pod.g3 .pod-vi{color:#3B6D11}

/* RANK BADGE */
.rb{width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:600}
.rb1{background:#FAEEDA;color:#633806}.rb2{background:#F1EFE8;color:#5F5E5A}.rb3{background:#EAF3DE;color:#27500A}.rbo{background:#f1f5f9;color:#64748b}

/* SAW VALUES */
.vr{color:#185FA5;font-size:10px}
.vw{color:#27500A;font-size:10px}
.vi-c{background:#E6F1FB !important;font-weight:600;color:#0C447C !important;font-size:11px}

/* ALERT VARIANTS */
.al-info{background:#E6F1FB;border-color:#3b82f6;color:#0C447C}
.al-ok{background:#EAF3DE;border-color:#22c55e;color:#27500A}
.al-warn{background:#FAEEDA;border-color:#f59e0b;color:#633806}

/* PROGRESS BAR */
.pb{height:5px;background:#e2e8f0;border-radius:3px;overflow:hidden}
.pf{height:100%;background:#2563eb;border-radius:3px;transition:width .3s}
</style>
</head>
<body>

<div class="sb">
    <div class="sb-top">
        <div class="sb-logo-row">
            <div class="sb-logo" style="background:transparent;padding:0;overflow:hidden"><img src="{{ asset('images/logo-cia.png') }}" style="width:36px;height:36px;object-fit:contain;border-radius:6px;display:block"></div>
            <div class="sb-brand">
                <strong>PT Cempaka Indah Abadi</strong>
                <span>Palembang</span>
            </div>
        </div>
    </div>
    <div class="sb-nav">
        <div class="sb-grp">Utama</div>
        <a href="{{ route('dashboard') }}" class="sbi {{ request()->routeIs('dashboard') ? 'on' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        @if(in_array(auth()->user()->role, ['admin','direktur']))
        <div class="sb-grp">Data Master</div>
        <a href="{{ route('pengguna.index') }}" class="sbi {{ request()->routeIs('pengguna.*') ? 'on' : '' }}">
            <i class="ti ti-users-group"></i> Kelola Pengguna
        </a>
        <a href="{{ route('karyawan.index') }}" class="sbi {{ request()->routeIs('karyawan.index','karyawan.create','karyawan.edit') ? 'on' : '' }}">
            <i class="ti ti-id-badge-2"></i> Kelola Karyawan
        </a>
        <a href="{{ route('absensi.rekap') }}" class="sbi {{ request()->routeIs('absensi.*') ? 'on' : '' }}">
            <i class="ti ti-calendar-stats"></i> Absensi
        </a>
        @endif

        @if(auth()->user()->role === 'direktur')
        <a href="{{ route('kriteria.index') }}" class="sbi {{ request()->routeIs('kriteria.index','kriteria.create','kriteria.edit') ? 'on' : '' }}">
            <i class="ti ti-adjustments-horizontal"></i> Kriteria
        </a>
        <a href="{{ route('sub-kriteria.index') }}" class="sbi {{ request()->routeIs('sub-kriteria.*','kriteria.sub-kriteria*') ? 'on' : '' }}" style="padding-left:28px;font-size:11px">
            <i class="ti ti-list-details"></i> Sub-Kriteria
        </a>

        <div class="sb-grp">Penilaian</div>
        <a href="{{ route('periode.index') }}" class="sbi {{ request()->routeIs('periode.*','penilaian.*','ranking.hitung') ? 'on' : '' }}">
            <i class="ti ti-clipboard-check"></i> Penilaian
        </a>
        @endif

        <div class="sb-grp">Laporan</div>
        @if(auth()->user()->role === 'direktur')
        <a href="{{ route('ranking.index') }}" class="sbi {{ request()->routeIs('ranking.index','ranking.hasil','ranking.cetak') ? 'on' : '' }}">
            <i class="ti ti-trophy"></i> Hasil Ranking
        </a>
        @endif
        @if(in_array(auth()->user()->role, ['admin','karyawan']))
        <a href="{{ route('absensi.pribadi') }}" class="sbi {{ request()->routeIs('absensi.pribadi') ? 'on' : '' }}">
            <i class="ti ti-calendar-user"></i> Absensi Saya
        </a>
        <a href="{{ route('karyawan.nilai') }}" class="sbi {{ request()->routeIs('karyawan.nilai') ? 'on' : '' }}">
            <i class="ti ti-chart-bar"></i> Nilai Saya
        </a>
        @endif
    </div>
    <div style="padding:10px 12px;border-top:1px solid rgba(255,255,255,.06);background:#0f172a">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                style="width:100%;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#ef4444;cursor:pointer;padding:9px;border-radius:8px;display:flex;align-items:center;justify-content:center;gap:8px;font-size:13px;font-weight:600;transition:background .15s"
                onmouseover="this.style.background='rgba(239,68,68,.25)'"
                onmouseout="this.style.background='rgba(239,68,68,.15)'">
                <i class="ti ti-logout" style="font-size:16px"></i> Keluar
            </button>
        </form>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <div style="flex:1">
            <div style="font-size:15px;font-weight:700;color:#1e293b;line-height:1.2">@yield('title')</div>
        </div>
        <div class="topbar-right">
            @php $namaTopbar = auth()->user()->karyawan?->nama ?? auth()->user()->username; @endphp
            <a href="{{ route('profil.show') }}" style="text-decoration:none;display:flex;align-items:center;gap:8px" title="Lihat Profil">
                <div style="width:32px;height:32px;border-radius:50%;background:#1e40af;display:flex;align-items:center;justify-content:center;color:#bfdbfe;font-size:11px;font-weight:700;transition:opacity .15s" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    {{ strtoupper(substr($namaTopbar,0,2)) }}
                </div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:#1e293b">{{ $namaTopbar }}</div>
                    <div style="font-size:10px;color:#64748b">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </a>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
        <div class="alert-spk al-ok">
            <i class="ti ti-check-circle"></i>{{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-spk al-danger" style="background:#FCEBEB;border-color:#ef4444;color:#791F1F">
            <i class="ti ti-alert-circle"></i>{{ session('error') }}
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>