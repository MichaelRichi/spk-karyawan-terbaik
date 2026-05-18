@extends('layouts.app')
@section('title','Buat Periode Baru')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Buat Periode Baru</div>
        <div class="ph-sub">Periode akan langsung aktif menggunakan kriteria & bobot yang berlaku saat ini</div>
    </div>
    <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

@if(!$bisaBuat)
{{-- Tidak bisa buat periode - bobot belum 100% atau belum ada kriteria --}}
<div class="card" style="max-width:520px;border-color:#fca5a5">
    <div style="padding:24px;text-align:center">
        <div style="width:56px;height:56px;background:#FCEBEB;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
            <i class="ti ti-alert-triangle" style="font-size:28px;color:#ef4444"></i>
        </div>
        @if($kriteria->isEmpty())
        <div style="font-weight:700;font-size:15px;color:#1e293b;margin-bottom:8px">Belum Ada Kriteria</div>
        <div style="color:#64748b;font-size:13px;margin-bottom:20px">
            Tambahkan kriteria penilaian beserta sub-kriteria dan bobotnya terlebih dahulu sebelum membuat periode.
        </div>
        @else
        <div style="font-weight:700;font-size:15px;color:#1e293b;margin-bottom:8px">Total Bobot Belum 100%</div>
        <div style="color:#64748b;font-size:13px;margin-bottom:6px">
            Total bobot kriteria saat ini <strong style="color:#ef4444">{{ $totalBobot }}%</strong>.
            Harus tepat <strong>100%</strong> sebelum bisa membuat periode.
        </div>
        <div style="background:#f8fafc;border-radius:8px;padding:10px 12px;margin:12px 0;text-align:left">
            @foreach($kriteria as $k)
            <div style="display:flex;justify-content:space-between;font-size:12px;padding:3px 0;{{ !$loop->last?'border-bottom:0.5px solid #e2e8f0':'' }}">
                <span style="color:#374151">{{ $k->nama }}</span>
                <span style="font-weight:600;color:#185FA5">{{ $k->bobot_default }}%</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0 2px;border-top:1px solid #cbd5e1;margin-top:4px">
                <span style="font-weight:700">Total</span>
                <span style="font-weight:700;color:{{ $totalBobot==100?'#27500A':'#ef4444' }}">{{ $totalBobot }}%</span>
            </div>
        </div>
        @endif
        <a href="{{ route('kriteria.index') }}" class="btn btn-primary">
            <i class="ti ti-adjustments-horizontal"></i> Atur Kriteria & Bobot
        </a>
    </div>
</div>

@else
{{-- Bisa buat periode --}}
<div class="card" style="max-width:520px">
    <div class="card-header"><i class="ti ti-calendar-plus"></i> Detail Periode</div>
    <div style="padding:16px 20px">

        {{-- Ringkasan kriteria aktif --}}
        <div style="background:#f8fafc;border-radius:8px;padding:10px 14px;margin-bottom:18px">
            <div style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">
                Kriteria yang akan dipakai
            </div>
            @foreach($kriteria as $k)
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:4px 0;{{ !$loop->last?'border-bottom:0.5px solid #e2e8f0':'' }}">
                <div>
                    <span style="font-weight:600;color:#1e293b">{{ $k->nama }}</span>
                    <span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}" style="font-size:9px;margin-left:5px">{{ $k->jenis }}</span>
                </div>
                <span style="font-weight:700;color:#185FA5">{{ $k->bobot_default }}%</span>
            </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;font-size:12px;padding:6px 0 2px;border-top:1px solid #cbd5e1;margin-top:4px">
                <span style="font-weight:700">Total Bobot</span>
                <span style="font-weight:700;color:#27500A">100%</span>
            </div>
        </div>

        <form method="POST" action="{{ route('periode.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Bulan <span style="color:#ef4444">*</span></label>
                <select name="bulan" class="form-select" required>
                    <option value="">-- Pilih Bulan --</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                    <option value="{{ $i+1 }}" {{ old('bulan')==$i+1?'selected':'' }}>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Tahun <span style="color:#ef4444">*</span></label>
                <input type="number" name="tahun" class="form-control"
                    value="{{ old('tahun', date('Y')) }}"
                    min="2000" max="2100" required>
            </div>
            <div style="display:flex;gap:8px">
                <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-player-play"></i> Buat & Aktifkan Periode
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection