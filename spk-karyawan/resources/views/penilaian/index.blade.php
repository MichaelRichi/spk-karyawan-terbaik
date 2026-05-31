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
    <div class="d-flex gap-2 align-items-start">
        <a href="{{ route('periode.show', $periode) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        @if($selesaiSemua)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalHitungSAW">
            <i class="ti ti-calculator"></i> Hitung Penilaian
        </button>
        @else
        <button class="btn btn-primary" style="opacity:.4;cursor:not-allowed" disabled>
            <i class="ti ti-calculator"></i> Hitung Penilaian
        </button>
        @endif
    </div>
</div>

<div class="steps">
    <div class="step done"><i class="ti ti-check"></i> Buat Periode</div>
    <span class="step-arr">›</span>
    <div class="step done"><i class="ti ti-check"></i> Atur Bobot</div>
    <span class="step-arr">›</span>
    <div class="step now"><i class="ti ti-pencil-check"></i> Input Penilaian</div>
    <span class="step-arr">›</span>
    <div class="step"><i class="ti ti-calculator"></i> Hitung Penilaian</div>
    <span class="step-arr">›</span>
    <div class="step"><i class="ti ti-lock"></i> Selesai</div>
</div>

@if(!$selesaiSemua)
<div class="alert-spk al-warn">
    <i class="ti ti-alert-triangle"></i>
    {{ $karyawan->count() - $penilaianSelesai->count() }} karyawan belum dinilai. Tombol Hitung Penilaian akan aktif setelah semua karyawan selesai dinilai.
</div>
@else
<div class="alert-spk al-ok">
    <i class="ti ti-check-circle"></i>
    Semua karyawan sudah dinilai. Klik <strong>Hitung Penilaian</strong> untuk memproses hasil akhir.
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
                        <div style="font-size:10px;color:#64748b">{{ $k->divisi }}</div>
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

{{-- Modal Konfirmasi Hitung Penilaian --}}
<div class="modal fade" id="modalHitungSAW" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.15)">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:28px 24px;text-align:center">
                <div style="width:60px;height:60px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;border:2px solid rgba(255,255,255,.3)">
                    <i class="ti ti-calculator" style="font-size:30px;color:#fff"></i>
                </div>
                <div style="color:#fff;font-weight:700;font-size:17px;margin-bottom:4px">Jalankan Perhitungan SAW?</div>
                <div style="color:rgba(255,255,255,.7);font-size:11px;letter-spacing:.5px;text-transform:uppercase">Simple Additive Weighting</div>
            </div>

            {{-- Body --}}
            <div style="padding:20px 24px">

                {{-- Ringkasan --}}
                <div style="background:#f8fafc;border-radius:10px;border:0.5px solid #e2e8f0;overflow:hidden;margin-bottom:14px">
                    <div style="background:#f1f5f9;padding:8px 14px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;border-bottom:0.5px solid #e2e8f0">
                        Ringkasan Penilaian
                    </div>
                    <div style="padding:0 14px">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:0.5px solid #f1f5f9">
                            <span style="font-size:12px;color:#64748b">Karyawan dinilai</span>
                            <span style="font-size:13px;font-weight:700;color:#16a34a;background:#dcfce7;padding:2px 10px;border-radius:20px">{{ $karyawan->count() }} / {{ $karyawan->count() }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:0.5px solid #f1f5f9">
                            <span style="font-size:12px;color:#64748b">Total kriteria</span>
                            <span style="font-size:13px;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:2px 10px;border-radius:20px">{{ $periode->periodeKriteria->count() }} kriteria</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0">
                            <span style="font-size:12px;color:#64748b">Periode</span>
                            <span style="font-size:13px;font-weight:600;color:#1e293b">{{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun }}</span>
                        </div>
                    </div>
                </div>

                {{-- Peringatan --}}
                <div style="background:#fffbeb;border:0.5px solid #fcd34d;border-radius:8px;padding:10px 12px;margin-bottom:18px;display:flex;gap:8px;align-items:flex-start">
                    <i class="ti ti-alert-triangle" style="color:#d97706;font-size:15px;flex-shrink:0;margin-top:1px"></i>
                    <span style="font-size:11px;color:#92400e;line-height:1.5">Setelah dihitung, periode akan <strong>dikunci</strong> dan nilai tidak dapat diubah lagi.</span>
                </div>

                {{-- Tombol --}}
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="flex:1;justify-content:center;padding:8px">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('ranking.hitung', $periode) }}" style="flex:1.5">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100"
                            style="justify-content:center;padding:8px;background:#2563eb;border-color:#2563eb">
                            <i class="ti ti-calculator"></i> Ya, Hitung Sekarang
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection