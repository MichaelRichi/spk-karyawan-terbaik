@extends('layouts.app')
@section('title','Sub-Kriteria')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Sub-Kriteria</div>
        <div class="ph-sub">Semua skala penilaian dari seluruh kriteria</div>
    </div>
</div>

@foreach($kriteria as $k)
<div class="card">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-weight:600">{{ $k->nama }}</span>
            <span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ $k->jenis }}</span>
            <span class="badge bg-gray-soft">Bobot: {{ $k->bobot_default }}%</span>
        </div>
        <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus"></i> Kelola Skala
        </a>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th class="text-center" style="width:80px">Skor</th><th>Nama</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($k->subKriteria->sortByDesc('skor') as $sk)
            <tr>
                <td class="text-center">
                    <span class="badge bg-primary" style="font-size:13px;padding:4px 10px">{{ $sk->skor }}</span>
                </td>
                <td style="font-weight:600">{{ $sk->nama }}</td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <form action="{{ route('kriteria.sub-kriteria.destroy', [$k, $sk]) }}"
                            method="POST" class="d-inline" onsubmit="return confirm('Hapus skala ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-3" style="color:#64748b">Belum ada skala. <a href="{{ route('kriteria.sub-kriteria', $k) }}">Tambah sekarang</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach
@endsection