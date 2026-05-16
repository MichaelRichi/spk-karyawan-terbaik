@extends('layouts.app')
@section('title','Atur Bobot — '.$periode->bulan.'/'.$periode->tahun)
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Atur Bobot — {{ $periode->bulan }}/{{ $periode->tahun }}</div>
        <div class="ph-sub">Perubahan bobot hanya berlaku untuk periode ini. Periode lain tidak terpengaruh.</div>
    </div>
    <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

@php $total = $periodeKriteria->sum('bobot'); @endphp
<div class="alert-spk {{ $total==100?'al-ok':'al-warn' }}">
    <i class="ti ti-{{ $total==100?'check-circle':'alert-triangle' }}"></i>
    Total bobot: <strong>{{ $total }}%</strong>
    @if($total != 100) — harus tepat 100% sebelum periode dapat diaktifkan @endif
</div>

<div class="card" style="max-width:500px">
    <div class="card-header"><i class="ti ti-sliders"></i> Bobot Kriteria Periode Ini</div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('periode.bobot.update', $periode) }}">
            @csrf @method('PUT')
            @foreach($periodeKriteria as $pk)
            <div class="mb-3">
                <label class="form-label">
                    {{ $pk->nama_kriteria }}
                    <span class="badge {{ $pk->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }} ms-1">{{ $pk->jenis }}</span>
                </label>
                <div class="input-group">
                    <input type="number" name="bobot[{{ $pk->id }}]" class="form-control"
                        value="{{ old('bobot.'.$pk->id, $pk->bobot) }}" min="0" max="100" step="0.01" required>
                    <span class="input-group-text" style="background:#f8fafc;border:0.5px solid #e2e8f0;font-size:12px">%</span>
                </div>
            </div>
            @endforeach
            <div style="display:flex;gap:8px;margin-top:16px;padding-top:14px;border-top:0.5px solid #e2e8f0">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i> Simpan Bobot
                </button>
                @if($total == 100)
                <form method="POST" action="{{ route('periode.aktifkan', $periode) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-success-soft"
                        onclick="return confirm('Aktifkan periode ini? Setelah aktif, bobot tidak dapat diubah.')">
                        <i class="ti ti-player-play"></i> Aktifkan Periode
                    </button>
                </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection