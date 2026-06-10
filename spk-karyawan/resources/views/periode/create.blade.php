@extends('layouts.app')
@section('title','Buat Periode Baru')
@section('content')

<div class="card" style="margin-bottom:16px;max-width:560px;margin-left:auto;margin-right:auto">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Buat Periode Baru</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Periode akan langsung aktif menggunakan kriteria & bobot yang berlaku saat ini</div>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">

@if(!$bisaBuat)
{{-- Tidak bisa buat periode --}}
<div class="card" style="border-color:#fca5a5;overflow:hidden">
    <div style="background:linear-gradient(135deg,#fee2e2,#fef2f2);padding:32px 24px;text-align:center;border-bottom:1px solid #fca5a5">
        <div style="width:64px;height:64px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 4px 12px rgba(239,68,68,.2)">
            <i class="ti ti-alert-triangle" style="font-size:32px;color:#ef4444"></i>
        </div>
        @if($kriteria->isEmpty())
        <div style="font-weight:700;font-size:16px;color:#1e293b;margin-bottom:6px">Belum Ada Kriteria</div>
        <div style="color:#64748b;font-size:13px">Tambahkan kriteria penilaian beserta sub-kriteria dan bobotnya terlebih dahulu.</div>
        @else
        <div style="font-weight:700;font-size:16px;color:#1e293b;margin-bottom:6px">Total Bobot Belum 100%</div>
        <div style="color:#64748b;font-size:13px">
            Total bobot saat ini <strong style="color:#ef4444">{{ $totalBobot }}%</strong> — harus tepat <strong>100%</strong>.
        </div>
        @endif
    </div>
    <div style="padding:20px 24px">
        @if(!$kriteria->isEmpty())
        <div style="margin-bottom:16px">
            @foreach($kriteria as $k)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;{{ !$loop->last?'border-bottom:0.5px solid #f1f5f9':'' }}">
                <div style="display:flex;align-items:center;gap:8px">
                    <span style="font-size:13px;font-weight:600;color:#1e293b">{{ $k->nama }}</span>
                    <span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}" style="font-size:9px">{{ $k->jenis }}</span>
                </div>
                <span style="font-weight:700;color:#185FA5">{{ $k->bobot }}%</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:2px solid #e2e8f0;margin-top:4px">
                <span style="font-weight:700;color:#1e293b">Total</span>
                <span style="font-weight:700;color:#ef4444">{{ $totalBobot }}%</span>
            </div>
        </div>
        @endif
        <a href="{{ route('kriteria.index') }}" class="btn btn-primary w-100">
            <i class="ti ti-adjustments-horizontal"></i> Atur Kriteria & Bobot
        </a>
    </div>
</div>

@else
{{-- Bisa buat periode --}}
<div class="card" style="overflow:hidden">
    {{-- Header card --}}
    <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:24px;text-align:center">
        <div style="width:56px;height:56px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border:2px solid rgba(255,255,255,.3)">
            <i class="ti ti-calendar-plus" style="font-size:28px;color:#fff"></i>
        </div>
        <div style="color:#fff;font-weight:700;font-size:16px;margin-bottom:3px">Buat Periode Penilaian Baru</div>
        <div style="color:rgba(255,255,255,.7);font-size:11px">Periode akan langsung aktif setelah dibuat</div>
    </div>

    <div style="padding:20px 24px">

        {{-- Ringkasan kriteria --}}
        <div style="background:#f8fafc;border-radius:10px;border:0.5px solid #e2e8f0;overflow:hidden;margin-bottom:20px">
            <div style="background:#f1f5f9;padding:8px 14px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;border-bottom:0.5px solid #e2e8f0">
                <i class="ti ti-list-check" style="font-size:11px"></i> Kriteria yang Akan Dipakai
            </div>
            <div style="padding:0 14px">
                @foreach($kriteria as $k)
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

        {{-- Form --}}
        <form method="POST" action="{{ route('periode.store') }}">
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
                    <i class="ti ti-player-play"></i> Buat & Aktifkan Periode
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