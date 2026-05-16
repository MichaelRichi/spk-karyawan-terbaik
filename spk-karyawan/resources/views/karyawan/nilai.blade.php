@extends('layouts.app')
@section('title','Nilai Saya')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Riwayat Penilaian — {{ $karyawan->nama }}</div>
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr><th>#</th><th>Periode</th><th class="text-center">Nilai Vi</th><th class="text-center">Ranking</th><th class="text-center">Detail</th></tr>
        </thead>
        <tbody>
            @forelse($riwayat as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->periode->bulan }}/{{ $r->periode->tahun }}</td>
                <td class="text-center fw-semibold text-primary">{{ number_format($r->nilai_preferensi,4) }}</td>
                <td class="text-center">
                    <span class="badge bg-{{ $r->ranking==1?'warning text-dark':'secondary' }}">#{{ $r->ranking }}</span>
                </td>
                <td class="text-center">
                    <a href="{{ route('ranking.hasil',$r->periode) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data penilaian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection