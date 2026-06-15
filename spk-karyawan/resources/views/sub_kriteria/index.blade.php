@extends('layouts.app')
@section('title','Sub-Kriteria — '.$kriteria->nama)
@section('content')
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Sub-Kriteria — {{ $kriteria->nama }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">
                <span class="badge {{ $kriteria->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ $kriteria->jenis }}</span>
                &nbsp;Bobot default: <strong>{{ $kriteria->bobot }}%</strong>
            </div>
        </div>
        <div style="display:flex;gap:8px">
            <a href="{{ route('sub-kriteria.index') }}" class="btn" style="background:#475569;border:1px solid #475569;color:#fff;font-weight:600">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('kriteria.sub-kriteria.create', $kriteria) }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Tambah Sub-Kriteria
            </a>
        </div>
    </div>
</div>

<div class="alert-spk {{ $kriteria->jenis=='benefit'?'al-ok':'al-info' }}">
    <i class="ti ti-info-circle"></i>
    @if($kriteria->jenis=='benefit')
        <strong>Benefit:</strong> Skor tertinggi = nilai terbaik. Rumus normalisasi: r = x / Max(x)
    @else
        <strong>Cost:</strong> Skor terendah = nilai terbaik. Rumus normalisasi: r = Min(x) / x
    @endif
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-list-details"></i> Skala Penilaian</span>
        <span class="badge bg-gray-soft" style="font-size:12px;padding:4px 11px">{{ $kriteria->subKriteria->count() }} skala</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th class="text-center" style="width:80px;color:#475569;font-weight:700">Skor</th><th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-file-description"></i> Deskripsi</span></th><th class="text-center" style="color:#475569;font-weight:700">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($kriteria->jenis === 'cost' ? $kriteria->subKriteria->sortBy('skor') : $kriteria->subKriteria->sortByDesc('skor') as $sk)
            <tr>
                <td class="text-center">
                    <span class="badge bg-primary" style="font-size:13px;padding:4px 10px">{{ $sk->skor }}</span>
                </td>
                <td style="font-weight:700;color:#1e293b;font-size:14px">{{ $sk->nama }}</td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('kriteria.sub-kriteria.edit', [$kriteria->id, $sk->id]) }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <form action="{{ route('kriteria.sub-kriteria.destroy', [$kriteria, $sk]) }}"
                            method="POST" class="d-inline" onsubmit="return confirm('Hapus skala ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i> Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-4" style="color:#64748b">Belum ada skala penilaian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection