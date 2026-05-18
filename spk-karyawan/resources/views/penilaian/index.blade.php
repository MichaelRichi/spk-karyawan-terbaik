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
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalHitungSAW">
        <i class="ti ti-calculator"></i> Hitung SAW
    </button>
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

{{-- Modal Konfirmasi Hitung SAW --}}
<div class="modal fade" id="modalHitungSAW" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="border:0.5px solid #e2e8f0;border-radius:12px;overflow:hidden">
            <div style="background:#2563eb;padding:24px;text-align:center">
                <div style="width:56px;height:56px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                    <i class="ti ti-calculator" style="font-size:28px;color:#fff"></i>
                </div>
                <div style="color:#fff;font-weight:700;font-size:16px">Jalankan Perhitungan SAW?</div>
                <div style="color:rgba(255,255,255,.8);font-size:12px;margin-top:4px">Simple Additive Weighting</div>
            </div>
            <div style="padding:20px">
                <div style="background:#f8fafc;border-radius:8px;padding:12px;margin-bottom:14px">
                    <div style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">
                        Ringkasan Penilaian
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:3px 0;border-bottom:0.5px solid #e2e8f0">
                        <span style="color:#374151">Total karyawan dinilai</span>
                        <span style="font-weight:600;color:#27500A">{{ $karyawan->count() }} / {{ $karyawan->count() }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:3px 0;border-bottom:0.5px solid #e2e8f0">
                        <span style="color:#374151">Total kriteria</span>
                        <span style="font-weight:600;color:#185FA5">{{ $periode->periodeKriteria->count() }} kriteria</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;padding:3px 0">
                        <span style="color:#374151">Periode</span>
                        <span style="font-weight:600">{{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun }}</span>
                    </div>
                </div>
                <div class="alert-spk al-warn" style="margin-bottom:14px;font-size:11px">
                    <i class="ti ti-alert-triangle"></i>
                    Setelah dihitung, periode akan dikunci dan nilai <strong>tidak dapat diubah</strong> lagi.
                </div>
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="flex:1;justify-content:center">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('ranking.hitung', $periode) }}" style="flex:1">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" style="justify-content:center">
                            <i class="ti ti-calculator"></i> Ya, Hitung Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection