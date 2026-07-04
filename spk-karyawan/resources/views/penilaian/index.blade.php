@extends('layouts.app')
@section('title','Input Penilaian — '.(['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun)
@section('content')
@php
    $namaBulan = (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun;
    $tipeLabels = ['tetap'=>'Karyawan Tetap','tidak_tetap'=>'Karyawan Tidak Tetap'];
    $selesaiSemua = $karyawan->count() > 0 && $karyawan->every(fn($k) => $penilaianSelesai->contains($k->id));
    $belum = $karyawan->count() - $penilaianSelesai->count();
@endphp
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Input Penilaian — {{ $namaBulan }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Penilaian dipisah per jenis kepegawaian. Pilih tab tipe karyawan, lalu isi nilai dan hitung rankingnya masing-masing.</div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('periode.show', $periode) }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            @if($selesaiSemua)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalHitungSAW">
                <i class="ti ti-calculator"></i> Hitung {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}
            </button>
            @else
            <button class="btn btn-primary" style="opacity:.4;cursor:not-allowed" disabled>
                <i class="ti ti-calculator"></i> Hitung {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}
            </button>
            @endif
        </div>
    </div>

    {{-- TAB tipe kepegawaian --}}
    <div style="display:flex;gap:8px;padding:0 24px 16px;flex-wrap:wrap">
        @foreach(['tetap','tidak_tetap'] as $t)
        @php $r = $ringkasanTab[$t]; $aktif = $t===$tipe; @endphp
        <a href="{{ route('penilaian.index', ['periode'=>$periode->id,'tipe'=>$t]) }}"
           style="text-decoration:none;display:flex;align-items:center;gap:8px;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:700;
                  {{ $aktif ? 'background:#2563eb;color:#fff;box-shadow:0 2px 6px rgba(37,99,235,.35)' : 'background:#e2e8f0;color:#475569;border:1px solid #cbd5e1' }}">
            {{ $tipeLabels[$t] }}
            <span style="font-size:11px;font-weight:700;padding:1px 8px;border-radius:20px;{{ $aktif?'background:rgba(255,255,255,.25);color:#fff':'background:#cbd5e1;color:#475569' }}">{{ $r['selesai'] }}/{{ $r['total'] }}</span>
            @if($r['dihitung'])<i class="ti ti-circle-check-filled" style="font-size:15px;color:{{ $aktif?'#bbf7d0':'#16a34a' }}"></i>@endif
        </a>
        @endforeach
    </div>
</div>

@if($karyawan->count() === 0)
<div class="alert-spk al-warn"><i class="ti ti-alert-triangle"></i> Belum ada karyawan {{ $tipe=='tetap'?'tetap':'tidak tetap' }} yang aktif. Tandai tipe karyawan di menu Kelola Karyawan.</div>
@elseif(!$selesaiSemua)
<div class="alert-spk al-warn">
    <i class="ti ti-alert-triangle"></i>
    {{ $belum }} karyawan {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }} belum dinilai. Tombol Hitung akan aktif setelah semua selesai dinilai.
</div>
@else
<div class="alert-spk al-ok">
    <i class="ti ti-check-circle"></i>
    Semua karyawan {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }} sudah dinilai. Klik <strong>Hitung {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}</strong> untuk memproses ranking kelompok ini.
</div>
@endif

<style>
.pn-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:12px;margin-bottom:16px}
.pn-stat{background:#fff;border:1px solid #e9eef5;border-radius:12px;padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:10px;border-left:4px solid var(--ac)}
.pn-stat-lbl{font-size:12px;color:#64748b;font-weight:600;margin-bottom:5px}
.pn-stat-val{font-size:24px;font-weight:800;line-height:1}
.pn-stat-ic{width:46px;height:46px;border-radius:12px;background:var(--bg);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pn-stat-ic i{font-size:23px;color:var(--ac)}
</style>

<div class="pn-stats">
    <div class="pn-stat" style="--ac:#16a34a;--bg:#dcfce7">
        <div><div class="pn-stat-lbl">Sudah Dinilai</div><div class="pn-stat-val" style="color:#16a34a">{{ $penilaianSelesai->count() }}</div></div>
        <div class="pn-stat-ic"><i class="ti ti-user-check"></i></div>
    </div>
    <div class="pn-stat" style="--ac:{{ $belum>0?'#dc2626':'#16a34a' }};--bg:{{ $belum>0?'#fee2e2':'#dcfce7' }}">
        <div><div class="pn-stat-lbl">Belum Dinilai</div><div class="pn-stat-val" style="color:{{ $belum>0?'#dc2626':'#16a34a' }}">{{ $belum }}</div></div>
        <div class="pn-stat-ic"><i class="ti ti-user-exclamation"></i></div>
    </div>
    <div class="pn-stat" style="--ac:#2563eb;--bg:#dbeafe">
        <div><div class="pn-stat-lbl">Total Kriteria</div><div class="pn-stat-val" style="color:#1e293b">{{ $periodeKriteria->count() }}</div></div>
        <div class="pn-stat-ic"><i class="ti ti-list-check"></i></div>
    </div>
    @php $tb = $periodeKriteria->sum('bobot'); @endphp
    <div class="pn-stat" style="--ac:#7c3aed;--bg:#ede9fe">
        <div><div class="pn-stat-lbl">Total Bobot</div><div class="pn-stat-val" style="color:#7c3aed">{{ rtrim(rtrim(number_format($tb,2),'0'),'.') }}%</div></div>
        <div class="pn-stat-ic"><i class="ti ti-percentage"></i></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-users"></i> Status Penilaian — {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}</span>
        <span style="font-size:13px;color:#64748b">{{ $penilaianSelesai->count() }} / {{ $karyawan->count() }} Selesai</span>
    </div>
    <div style="overflow-x:auto">
        <table class="table mb-0" style="min-width:600px">
            <thead>
                <tr>
                    <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-user"></i> Karyawan</span></th>
                    @foreach($periodeKriteria as $pk)
                    <th class="text-center" style="font-size:11px;color:#475569;font-weight:700">{{ $pk->nama_kriteria }}<br><span style="color:#64748b;font-weight:600">{{ $pk->bobot }}%</span></th>
                    @endforeach
                    <th class="text-center" style="color:#475569;font-weight:700">Status</th>
                    <th class="text-center" style="color:#475569;font-weight:700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $k)
                @php $sudah = $penilaianSelesai->contains($k->id); @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;color:#1e293b;font-size:14px">{{ $k->nama }}</div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:500">ID: {{ $k->id }}</div>
                    </td>
                    @foreach($periodeKriteria as $pk)
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
                        <span class="badge {{ $sudah?'bg-success-soft':'bg-warning-soft' }}" style="font-size:12px;padding:4px 11px">{{ $sudah?'Lengkap':'Belum' }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('penilaian.form', [$periode, $k]) }}" class="btn {{ $sudah?'btn-outline-primary':'btn-primary' }} btn-sm">{{ $sudah?'Edit':'Input Nilai' }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $periodeKriteria->count() + 3 }}" class="text-center py-4" style="color:#64748b">Tidak ada karyawan {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Konfirmasi Hitung Penilaian (tab aktif) --}}
<div class="modal fade" id="modalHitungSAW" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:none;border-radius:14px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.15)">
            <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:28px 24px;text-align:center">
                <div style="width:60px;height:60px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;border:2px solid rgba(255,255,255,.3)">
                    <i class="ti ti-calculator" style="font-size:30px;color:#fff"></i>
                </div>
                <div style="color:#fff;font-weight:700;font-size:17px;margin-bottom:4px">Hitung {{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}</div>
            </div>
            <div style="padding:20px 24px">
                <div style="background:#f8fafc;border-radius:10px;border:0.5px solid #e2e8f0;overflow:hidden;margin-bottom:14px">
                    <div style="background:#f1f5f9;padding:8px 14px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;border-bottom:0.5px solid #e2e8f0">Ringkasan Penilaian</div>
                    <div style="padding:0 14px">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:0.5px solid #f1f5f9">
                            <span style="font-size:12px;color:#64748b">Kelompok</span>
                            <span style="font-size:13px;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:2px 10px;border-radius:20px">{{ ($tipe==='tetap'?'Karyawan Tetap':'Karyawan Tidak Tetap') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:0.5px solid #f1f5f9">
                            <span style="font-size:12px;color:#64748b">Karyawan dinilai</span>
                            <span style="font-size:13px;font-weight:700;color:#16a34a;background:#dcfce7;padding:2px 10px;border-radius:20px">{{ $karyawan->count() }} / {{ $karyawan->count() }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0">
                            <span style="font-size:12px;color:#64748b">Total kriteria</span>
                            <span style="font-size:13px;font-weight:700;color:#1d4ed8;background:#dbeafe;padding:2px 10px;border-radius:20px">{{ $periodeKriteria->count() }} kriteria</span>
                        </div>
                    </div>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="flex:1;justify-content:center;padding:8px">Batal</button>
                    <form method="POST" action="{{ route('ranking.hitung', $periode) }}" style="flex:1.5">
                        @csrf
                        <input type="hidden" name="tipe" value="{{ $tipe }}">
                        <button type="submit" class="btn btn-primary w-100" style="justify-content:center;padding:8px;background:#2563eb;border-color:#2563eb">
                            <i class="ti ti-calculator"></i> Ya, Hitung Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection