@extends('layouts.app')
@section('title','Dashboard')
@section('content')

<div class="ph">
    <div>
        <div class="ph-title">Dashboard</div>
        <div class="ph-sub">Selamat datang, {{ ucfirst(auth()->user()->role) }} {{ auth()->user()->username }}</div>
    </div>
    @if(auth()->user()->role === 'direktur')
    <a href="{{ route('periode.create') }}" class="btn btn-primary">
        <i class="ti ti-plus"></i> Buat Periode Baru
    </a>
    @endif
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-lbl">Total Karyawan</div>
        <div class="stat-val">{{ $totalKaryawan }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Periode Selesai</div>
        <div class="stat-val">{{ $totalPeriode }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Periode Aktif</div>
        <div class="stat-val" style="font-size:14px;margin-top:3px">
            {{ $periodeAktif ? (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun : '—' }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Karyawan Terbaik Terakhir</div>
        <div class="stat-val" style="font-size:14px;margin-top:3px">
            {{ $karyawanTerbaik?->karyawan?->nama ?? '—' }}
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    @if($periodeAktif)
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <span><i class="ti ti-calendar-event"></i> Periode Aktif</span>
                @if(auth()->user()->role === 'direktur')
                <a href="{{ route('penilaian.index', $periodeAktif) }}" class="btn btn-info-soft btn-sm">Input Nilai</a>
                @endif
            </div>
            <div style="padding:12px 14px">
                <div style="font-size:14px;font-weight:600;margin-bottom:8px">
                    {{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periodeAktif->bulan] ?? $periodeAktif->bulan).' '.$periodeAktif->tahun }}
                </div>
                @php $dinilai = $periodeAktif->penilaian->pluck('karyawan_id')->unique()->count(); @endphp
                <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:5px">
                    <span style="color:#64748b">Karyawan dinilai</span>
                    <span style="font-weight:600;color:{{ $dinilai < $totalKaryawan ? '#854F0B' : '#27500A' }}">{{ $dinilai }} / {{ $totalKaryawan }}</span>
                </div>
                <div class="pb"><div class="pf" style="width:{{ $totalKaryawan > 0 ? ($dinilai/$totalKaryawan*100) : 0 }}%"></div></div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-{{ $periodeAktif ? '6' : '12' }}">
        <div class="card h-100">
            <div class="card-header"><i class="ti ti-trophy"></i> Karyawan Terbaik Terakhir</div>
            <div style="padding:12px 14px;display:flex;align-items:center;gap:12px">
                <div style="width:44px;height:44px;background:#FAEEDA;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">🏆</div>
                <div>
                    <div style="font-size:14px;font-weight:600">{{ $karyawanTerbaik?->karyawan?->nama ?? '—' }}</div>
                    @if($karyawanTerbaik)
                    <div style="font-size:10px;color:#64748b">
                        {{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$karyawanTerbaik->periode->bulan] ?? $karyawanTerbaik->periode->bulan).' '.$karyawanTerbaik->periode->tahun }}
                        · Vi = {{ number_format($karyawanTerbaik->nilai_preferensi, 4) }}
                    </div>
                    <span class="badge bg-success-soft mt-1">Karyawan Terbaik</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="ti ti-clock-hour-4"></i> Riwayat Periode</div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Periode</th>
                <th>Status</th>
                <th>Karyawan Terbaik</th>
                <th class="text-center">Vi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $p)
            @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
            <tr>
                <td style="font-weight:600">{{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$p->bulan] ?? $p->bulan).' '.$p->tahun }}</td>
                <td>
                    <span class="badge {{ $p->status=='selesai'?'bg-success-soft':($p->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}">
                        {{ $p->status }}
                    </span>
                </td>
                <td>{{ $terbaik?->karyawan?->nama ?? '—' }}</td>
                <td class="text-center" style="color:#185FA5;font-weight:600">
                    {{ $terbaik ? number_format($terbaik->nilai_preferensi, 4) : '—' }}
                </td>
                <td class="text-center">
                    @if($p->status === 'selesai')
                    <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-info-soft btn-sm">Lihat Hasil</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada periode.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection