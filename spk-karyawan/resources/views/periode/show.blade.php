@extends('layouts.app')
@section('title','Detail Periode')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Periode {{ $periode->bulan }}/{{ $periode->tahun }}</div>
        <div class="ph-sub">
            <span class="badge {{ $periode->status=='selesai'?'bg-success-soft':($periode->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}">
                {{ $periode->status }}
            </span>
        </div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('periode.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        @if($periode->status === 'aktif')
        <a href="{{ route('penilaian.index', $periode) }}" class="btn btn-primary">
            <i class="ti ti-pencil-check"></i> Input Penilaian
        </a>
        @endif
        @if($periode->status === 'selesai')
        <a href="{{ route('ranking.hasil', $periode) }}" class="btn btn-success-soft">
            <i class="ti ti-trophy"></i> Lihat Hasil
        </a>
        @endif
    </div>
</div>

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
    @if($periode->status === 'draft')
    <div style="padding:10px 14px;border-top:0.5px solid #e2e8f0">
        <a href="{{ route('periode.bobot', $periode) }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-pencil"></i> Ubah Bobot
        </a>
    </div>
    @endif
</div>
@endsection