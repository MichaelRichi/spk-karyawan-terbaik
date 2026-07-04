@extends('layouts.app')
@section('title','Buat Periode Baru')
@section('content')
@php $tipeLabels = ['tetap'=>'Karyawan Tetap','tidak_tetap'=>'Karyawan Tidak Tetap']; @endphp

<div class="card" style="margin-bottom:16px;max-width:560px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Buat Periode Baru</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Satu periode mencakup seluruh karyawan. Pemisahan tetap &amp; tidak tetap dilakukan saat input penilaian. Kedua set kriteria harus bertotal 100%.</div>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">

@if($errors->any())
<div class="alert-spk al-danger" style="background:#FCEBEB;border-color:#ef4444;color:#791F1F;margin-bottom:14px">
    <i class="ti ti-alert-circle"></i>
    <div>
        <strong>Periode gagal dibuat:</strong>
        <ul style="margin:4px 0 0 16px;padding:0">
            @foreach($errors->all() as $e)<li style="font-size:12px">{{ $e }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

@if(!$bisaBuat)
{{-- Belum bisa buat periode: tampilkan status tiap set --}}
<div class="card" style="border-color:#fca5a5;overflow:hidden">
    <div style="background:linear-gradient(135deg,#fee2e2,#fef2f2);padding:28px 24px;text-align:center;border-bottom:1px solid #fca5a5">
        <div style="width:64px;height:64px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 4px 12px rgba(239,68,68,.2)">
            <i class="ti ti-alert-triangle" style="font-size:32px;color:#ef4444"></i>
        </div>
        <div style="font-weight:700;font-size:16px;color:#1e293b;margin-bottom:6px">Kriteria Belum Siap</div>
        <div style="color:#64748b;font-size:13px">Pastikan kedua set kriteria (Tetap &amp; Tidak Tetap) tidak kosong dan bertotal 100%.</div>
    </div>
    <div style="padding:20px 24px">
        @foreach(['tetap','tidak_tetap'] as $tp)
        @php $set = $setKriteria[$tp]; @endphp
        <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <span class="badge {{ $tp=='tetap'?'bg-info-soft':'bg-warning-soft' }}" style="font-size:11px">{{ $tipeLabels[$tp] }}</span>
                <span style="font-weight:700;font-size:13px;color:{{ $set['valid']?'#16a34a':'#ef4444' }}">
                    {{ $set['kriteria']->isEmpty() ? 'Belum ada kriteria' : $set['totalBobot'].'%' }}
                    {!! $set['valid'] ? '✓' : '' !!}
                </span>
            </div>
        </div>
        @endforeach
        <a href="{{ route('kriteria.index') }}" class="btn btn-primary w-100" style="margin-top:6px">
            <i class="ti ti-adjustments-horizontal"></i> Atur Kriteria &amp; Bobot
        </a>
    </div>
</div>

@else
{{-- Bisa buat periode --}}
<div class="card" style="overflow:hidden">
    <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:24px;text-align:center">
        <div style="width:56px;height:56px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border:2px solid rgba(255,255,255,.3)">
            <i class="ti ti-calendar-plus" style="font-size:28px;color:#fff"></i>
        </div>
        <div style="color:#fff;font-weight:700;font-size:16px;margin-bottom:3px">Buat Periode Penilaian Baru</div>
        <div style="color:rgba(255,255,255,.7);font-size:11px">Periode akan langsung aktif setelah dibuat</div>
    </div>

    <div style="padding:20px 24px">
        {{-- Ringkasan kedua set kriteria --}}
        @foreach(['tetap','tidak_tetap'] as $tp)
        @php $set = $setKriteria[$tp]; @endphp
        <div style="background:#f8fafc;border-radius:10px;border:0.5px solid #e2e8f0;overflow:hidden;margin-bottom:14px">
            <div style="background:#f1f5f9;padding:8px 14px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;border-bottom:0.5px solid #e2e8f0">
                <i class="ti ti-list-check" style="font-size:11px"></i> Kriteria {{ $tipeLabels[$tp] }}
            </div>
            <div style="padding:0 14px">
                @foreach($set['kriteria'] as $k)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:9px 0;{{ !$loop->last?'border-bottom:0.5px solid #f1f5f9':'' }}">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:6px;height:6px;border-radius:50%;background:{{ $k->jenis=='benefit'?'#16a34a':'#ef4444' }}"></div>
                        <span style="font-size:13px;font-weight:600;color:#1e293b">{{ $k->nama }}</span>
                        <span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}" style="font-size:9px">{{ $k->jenis }}</span>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:#185FA5">{{ $k->bobot }}%</span>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:9px 0;border-top:1.5px solid #cbd5e1;margin-top:2px">
                    <span style="font-weight:700;color:#1e293b;font-size:13px">Total Bobot</span>
                    <span style="font-weight:700;color:#16a34a;font-size:13px">100% ✓</span>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Form --}}
        <form method="POST" action="{{ route('periode.store') }}" style="margin-top:6px">
            @csrf
            <div class="row g-3 mb-4">
                <div class="col-7">
                    <label class="form-label">Bulan <span style="color:#ef4444">*</span></label>
                    <div class="select-wrap">
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ old('bulan')==$i+1?'selected':'' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('bulan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-5">
                    <label class="form-label">Tahun <span style="color:#ef4444">*</span></label>
                    <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                        value="{{ old('tahun', date('Y')) }}" min="2000" max="2100" required>
                    @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:8px">
                <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary" style="flex:1;justify-content:center">Batal</a>
                <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;background:#2563eb">
                    <i class="ti ti-player-play"></i> Buat &amp; Aktifkan Periode
                </button>
            </div>
        </form>
    </div>
</div>
@endif

</div>

@push('scripts')
<style>
.select-wrap{position:relative}
.select-wrap::after{content:'';position:absolute;right:12px;top:50%;transform:translateY(-50%);width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:6px solid #64748b;pointer-events:none}
.select-wrap select{appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer}
</style>
@endpush
@endsection