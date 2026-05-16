@extends('layouts.app')
@section('title','Periode Penilaian')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('periode.create') }}" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i> Buat Periode
    </a>
</div>

<div class="row g-3">
    @forelse($periode as $p)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ $p->bulan }}/{{ $p->tahun }}</h6>
                    <span class="badge bg-{{ $p->status=='selesai'?'success':($p->status=='aktif'?'primary':'secondary') }}">
                        {{ $p->status }}
                    </span>
                </div>
                @if($p->status !== 'draft')
                <div class="text-muted small mb-3">
                    Karyawan dinilai: {{ $p->penilaian->pluck('karyawan_id')->unique()->count() }} orang
                </div>
                @endif
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('periode.show',$p) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                    @if($p->status == 'draft')
                    <a href="{{ route('periode.bobot',$p) }}" class="btn btn-sm btn-outline-warning">Atur Bobot</a>
                    @endif
                    @if($p->status == 'aktif')
                    <a href="{{ route('penilaian.index',$p) }}" class="btn btn-sm btn-primary">Input Nilai</a>
                    @endif
                    @if($p->status == 'selesai')
                    <a href="{{ route('ranking.hasil',$p) }}" class="btn btn-sm btn-outline-success">Hasil</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5 text-muted">
            Belum ada periode. <a href="{{ route('periode.create') }}">Buat periode baru</a>
        </div>
    </div>
    @endforelse
</div>
@endsection