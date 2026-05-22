@extends('layouts.app')
@section('title','Penilaian — '.$karyawan->nama)
@section('content')
<div class="ph">
    <div style="display:flex;align-items:center;gap:10px">
        <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left"></i>
        </a>
        @php $initials = strtoupper(substr($karyawan->nama,0,2)); @endphp
        <div style="width:40px;height:40px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:#1e40af;flex-shrink:0">{{ $initials }}</div>
        <div>
            <div class="ph-title">{{ $karyawan->nama }}</div>
            <div class="ph-sub">{{ (['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan] ?? $periode->bulan).' '.$periode->tahun }} · Pilih skor untuk setiap kriteria</div>
        </div>
    </div>
    <button type="submit" form="form-penilaian" class="btn btn-primary">
        <i class="ti ti-device-floppy"></i> Simpan Penilaian
    </button>
</div>

<form id="form-penilaian" method="POST" action="{{ route('penilaian.simpan', [$periode, $karyawan]) }}">
    @csrf
    <div class="row g-3">
        @foreach($periode->periodeKriteria as $pk)
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <span>
                        <i class="ti ti-adjustments"></i>
                        {{ $pk->nama_kriteria }}
                        <span class="badge {{ $pk->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }} ms-1">{{ $pk->jenis }}</span>
                    </span>
                    <span class="badge bg-info-soft">{{ $pk->bobot }}%</span>
                </div>
                <div style="padding:12px 14px;display:flex;flex-direction:column;gap:6px">

                    @foreach($pk->jenis === 'cost' ? $pk->periodeSubKriteria->sortBy('skor') : $pk->periodeSubKriteria->sortByDesc('skor') as $psk)
                    @php $terpilih = isset($nilaiExisting[$pk->id]) && $nilaiExisting[$pk->id]->periode_sub_kriteria_id == $psk->id; @endphp
                    <label style="display:flex;align-items:flex-start;gap:10px;padding:8px 10px;border:{{ $terpilih?'1.5px solid #2563eb':'0.5px solid #e2e8f0' }};border-radius:7px;cursor:pointer;background:{{ $terpilih?'#E6F1FB':'#fff' }};transition:all .15s">
                        <input type="radio" name="penilaian[{{ $pk->id }}]" value="{{ $psk->id }}" {{ $terpilih?'checked':'' }} required style="margin-top:3px">
                        <div style="flex:1">
                            <div>
                                <span style="color:{{ $terpilih?'#0C447C':'#374151' }};font-weight:600;font-size:13px">{{ $psk->label }}</span>
                            </div>
                            @if($psk->keterangan)
                            <div style="color:#64748b;font-size:11px;margin-top:2px">
                                <i class="ti ti-info-circle" style="font-size:10px"></i> {{ $psk->keterangan }}
                            </div>
                            @endif
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:16px;display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy"></i> Simpan Penilaian
        </button>
        <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection