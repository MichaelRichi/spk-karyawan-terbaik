@extends('layouts.app')
@section('title','Input Penilaian — '.(['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun)
@section('content')
@php
    /** @var \Illuminate\Database\Eloquent\Collection $karyawan */
    /** @var \Illuminate\Support\Collection $penilaianSelesai */
    /** @var array $nilaiKaryawan */
@endphp
<div class="ph">
    <div>
        <div class="ph-title">Input Penilaian — {{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun }}</div>
        <div class="ph-sub">Pilih karyawan untuk mengisi atau mengedit nilai penilaian</div>
    </div>
    @php $selesaiSemua = $karyawan->every(fn($k) => $penilaianSelesai->contains($k->id)); @endphp
    @if($selesaiSemua)
    <form method="POST" action="{{ route('ranking.hitung', $periode) }}">
        @csrf
        <button class="btn btn-primary" onclick="return confirm('Jalankan perhitungan SAW?')">
            <i class="ti ti-calculator"></i> Hitung SAW
        </button>
    </form>
    @else
    <button class="btn btn-primary" style="opacity:.4;cursor:not-allowed" disabled>
        <i class="ti ti-calculator"></i> Hitung SAW
    </button>
    @endif
</div>

<div class="steps">
    <div class="step done"><i class="ti ti-check"></i> Buat Periode</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-check"></i> Atur Bobot</div>
    <span class="step-arr">›</span>
    <div class="step now"><i class="ti ti-pencil-check"></i> Input Penilaian</div>
    <span class="step-arr">›</span>
    <div class="step"><i class="ti ti-calculator"></i> Hitung SAW</div>
    <span class="step-arr">›</span>
    <div class="step"><i class="ti ti-lock"></i> Selesai</div>
</div>

@if(!$selesaiSemua)
<div class="alert-spk al-warn">
    <i class="ti ti-alert-triangle"></i>
    {{ $karyawan->count() - $penilaianSelesai->count() }} karyawan belum dinilai. Tombol Hitung SAW akan aktif setelah semua karyawan selesai dinilai.
</div>
@else
<div class="alert-spk al-ok">
    <i class="ti ti-check-circle"></i>
    Semua karyawan sudah dinilai. Klik <strong>Hitung SAW</strong> untuk memproses hasil akhir.
</div>
@endif

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-lbl">Sudah Dinilai</div>
        <div class="stat-val" style="color:#27500A">{{ $penilaianSelesai->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Belum Dinilai</div>
        <div class="stat-val" style="color:#A32D2D">{{ $karyawan->count() - $penilaianSelesai->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Total Kriteria</div>
        <div class="stat-val">{{ $periode->periodeKriteria->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-lbl">Total Bobot</div>
        <div class="stat-val" style="font-size:16px;color:#185FA5">{{ $periode->periodeKriteria->sum('bobot') }}%</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="ti ti-users"></i> Status Penilaian per Karyawan</span>
        <span style="font-size:11px;color:#64748b">{{ $penilaianSelesai->count() }} / {{ $karyawan->count() }} selesai</span>
    </div>
    <div style="overflow-x:auto">
        <table class="table mb-0" style="min-width:600px">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    @foreach($periode->periodeKriteria as $pk)
                    <th class="text-center" style="font-size:9px">{{ $pk->nama_kriteria }}<br><span style="color:#64748b">{{ $pk->bobot }}%</span></th>
                    @endforeach
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($karyawan as $k)
                @php $sudah = $penilaianSelesai->contains($k->id); @endphp
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $k->nama }}</div>
                        <div style="font-size:10px;color:#64748b">{{ $k->jabatan }}</div>
                    </td>
                    @foreach($periode->periodeKriteria as $pk)
                    @php $p = $nilaiKaryawan[$k->id][$pk->id] ?? null; @endphp
                    <td class="text-center">
                        @if($p)
                        <span class="badge bg-success-soft" style="font-size:11px">{{ $p->periodeSubKriteria?->skor ?? $p->nilai }}</span>
                        @else
                        <span style="color:#cbd5e1">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-center">
                        <span class="badge {{ $sudah?'bg-success-soft':'bg-warning-soft' }}">
                            {{ $sudah?'Lengkap':'Belum' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('penilaian.form', [$periode, $k]) }}"
                            class="btn {{ $sudah?'btn-outline-primary':'btn-primary' }} btn-sm">
                            {{ $sudah?'Edit':'Input Nilai' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection