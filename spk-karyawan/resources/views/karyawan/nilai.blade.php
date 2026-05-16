@extends('layouts.app')
@section('title','Nilai Saya')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Nilai Saya</div>
        <div class="ph-sub">Riwayat penilaian — {{ $karyawan->nama }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="ti ti-chart-bar"></i> Riwayat Penilaian</div>
    <table class="table mb-0">
        <thead>
            <tr><th>#</th><th>Periode</th><th class="text-center">Nilai Vi</th><th class="text-center">Ranking</th><th class="text-center">Detail</th></tr>
        </thead>
        <tbody>
            @forelse($riwayat as $r)
            <tr>
                <td style="color:#64748b">{{ $loop->iteration }}</td>
                <td style="font-weight:600">{{ $r->periode->bulan }}/{{ $r->periode->tahun }}</td>
                <td class="text-center" style="font-weight:600;color:#185FA5">{{ number_format($r->nilai_preferensi,4) }}</td>
                <td class="text-center">
                    @if($r->ranking == 1)
                        <span class="badge bg-warning-soft">🏆 #1</span>
                    @elseif($r->ranking == 2)
                        <span class="badge bg-gray-soft">🥈 #2</span>
                    @elseif($r->ranking == 3)
                        <span class="badge bg-success-soft">🥉 #3</span>
                    @else
                        <span class="badge bg-gray-soft">#{{ $r->ranking }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('ranking.hasil', $r->periode) }}" class="btn btn-info-soft btn-sm">
                        <i class="ti ti-eye"></i> Lihat
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4" style="color:#64748b">Belum ada data penilaian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection