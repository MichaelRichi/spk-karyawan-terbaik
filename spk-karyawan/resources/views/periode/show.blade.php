@extends('layouts.app')
@section('title','Detail Periode')
@section('content')
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        @php $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $labelBulan = ($namaBulan[$periode->bulan] ?? $periode->bulan).' '.$periode->tahun; @endphp
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Periode {{ $labelBulan }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
                <span class="badge {{ $periode->status=='selesai'?'bg-success-soft':($periode->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}">
                    {{ $periode->status }}
                </span>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('periode.index') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            @if($periode->status === 'aktif')
            <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-primary">
                <i class="ti ti-pencil-check"></i> Input Penilaian
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusPeriode">
                <i class="ti ti-trash"></i> Hapus Periode
            </button>
            @endif
            @if($periode->status === 'selesai')
            <a href="{{ route('ranking.hasil', $periode) }}" class="btn btn-success-soft">
                <i class="ti ti-trophy"></i> Lihat Hasil
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
@if($periode->status === 'aktif')
<div class="modal fade" id="modalHapusPeriode" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" style="color:#dc2626">
                    <i class="ti ti-alert-triangle"></i> Hapus Periode Aktif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Kamu akan menghapus periode <strong>{{ $labelBulan }}</strong> secara permanen.</p>
                <p class="mb-0" style="font-size:13px;color:#64748b">Data yang ikut terhapus:</p>
                <ul style="font-size:13px;color:#64748b;margin-top:4px">
                    <li>Semua nilai penilaian karyawan pada periode ini</li>
                    <li>Kriteria & bobot periode ini</li>
                </ul>
                <div class="alert alert-warning py-2 mt-2" style="font-size:13px">
                    <i class="ti ti-info-circle"></i> Data absensi <strong>tidak</strong> ikut terhapus — tetap aman di sistem.
                </div>
                <p class="mb-0 fw-600">Tindakan ini tidak bisa dibatalkan. Lanjutkan?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('periode.hapus', $periode) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash"></i> Hapus Periode
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="steps">
    <div class="step {{ in_array($periode->status,['draft','aktif','selesai'])?'done':'' }}">
        <i class="ti ti-check"></i> Buat Periode
    </div>
    <span class="step-arr">›</span>
    <div class="step {{ in_array($periode->status,['aktif','selesai'])?'done':($periode->status=='draft'?'now':'') }}">
        <i class="ti ti-{{ $periode->status=='draft'?'sliders':'check' }}"></i> Atur Bobot
    </div>
    <span class="step-arr">›</span>
    <div class="step {{ $periode->status=='aktif'?'now':($periode->status=='selesai'?'done':'') }}">
        <i class="ti ti-pencil-check"></i> Input Penilaian
    </div>
    <span class="step-arr">›</span>
    <div class="step {{ $periode->status=='selesai'?'done':'' }}">
        <i class="ti ti-calculator"></i> Hitung Penilaian
    </div>
    <span class="step-arr">›</span>
    <div class="step {{ $periode->status=='selesai'?'done':'' }}">
        <i class="ti ti-lock"></i> Selesai
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="ti ti-sliders"></i> Kriteria & Bobot Periode Ini</div>
    <table class="table mb-0">
        <thead>
            <tr><th>Kriteria</th><th>Tipe</th><th class="text-center">Bobot</th><th>Distribusi</th></tr>
        </thead>
        <tbody>
            @foreach($periode->periodeKriteria as $pk)
            <tr>
                <td style="font-weight:600">{{ $pk->nama_kriteria }}</td>
                <td><span class="badge {{ $pk->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ $pk->jenis }}</span></td>
                <td class="text-center" style="font-weight:600;color:#185FA5">{{ $pk->bobot }}%</td>
                <td style="min-width:120px">
                    <div class="pb"><div class="pf" style="width:{{ $pk->bobot }}%"></div></div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc">
                <td colspan="2" style="font-weight:600">Total Bobot</td>
                <td class="text-center" style="font-weight:600;color:{{ $periode->periodeKriteria->sum('bobot')==100?'#27500A':'#A32D2D' }}">
                    {{ $periode->periodeKriteria->sum('bobot') }}%
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</div>
@endsection