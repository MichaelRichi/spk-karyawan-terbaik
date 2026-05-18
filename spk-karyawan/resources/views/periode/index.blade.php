@extends('layouts.app')
@section('title','Periode Penilaian')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Periode Penilaian</div>
        <div class="ph-sub">Setiap periode menyimpan snapshot kriteria & bobot sendiri</div>
    </div>
    <a href="{{ route('periode.create') }}" class="btn btn-primary">
        <i class="ti ti-plus"></i> Buat Periode Baru
    </a>
</div>

<div class="row g-3">
    @forelse($periode as $p)
    <div class="col-md-4">
        <div class="card h-100" style="border-color:{{ $p->status=='aktif'?'#85B7EB':'#e2e8f0' }}">
            <div class="card-header">
                @php $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; $labelBulan = ($namaBulan[$p->bulan] ?? $p->bulan).' '.$p->tahun; @endphp
                <span style="font-weight:600">{{ $labelBulan }}</span>
                <span class="badge {{ $p->status=='selesai'?'bg-success-soft':($p->status=='aktif'?'bg-info-soft':'bg-gray-soft') }}">
                    {{ $p->status }}
                </span>
            </div>
            <div style="padding:12px 14px">
                @if($p->status === 'aktif')
                    @php $dinilai = $p->penilaian->pluck('karyawan_id')->unique()->count(); $total = \App\Models\Karyawan::aktif()->count(); @endphp
                    <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:5px">
                        <span style="color:#64748b">Karyawan dinilai</span>
                        <span style="font-weight:600;color:#854F0B">{{ $dinilai }} / {{ $total }}</span>
                    </div>
                    <div class="pb mb-3"><div class="pf" style="width:{{ $total > 0 ? ($dinilai/$total*100) : 0 }}%"></div></div>
                @elseif($p->status === 'selesai')
                    @php $terbaik = $p->hasilRanking->where('ranking',1)->first(); @endphp
                    <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:4px">
                        <span style="color:#64748b">Karyawan terbaik</span>
                        <span style="font-weight:600">{{ $terbaik?->karyawan?->nama ?? '—' }}</span>
                    </div>
                    <div style="font-size:11px;font-weight:600;color:#185FA5;margin-bottom:10px">
                        Vi = {{ $terbaik ? number_format($terbaik->nilai_preferensi, 4) : '—' }}
                    </div>
                @else
                    @php $total = $p->periodeKriteria->sum('bobot'); @endphp
                    <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:8px">
                        <span style="color:#64748b">Total bobot</span>
                        <span style="font-weight:600;color:{{ $total==100?'#27500A':'#A32D2D' }}">{{ $total }}%</span>
                    </div>
                    @if($total != 100)
                    <div class="alert-spk al-warn" style="padding:5px 8px;font-size:10px;margin-bottom:8px">
                        <i class="ti ti-alert-triangle"></i> Bobot belum 100%. Atur sebelum aktifkan.
                    </div>
                    @endif
                @endif

                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    <a href="{{ route('periode.show', $p) }}" class="btn btn-outline-secondary btn-sm">Detail</a>
                    @if($p->status === 'aktif')
                    <a href="{{ route('penilaian.index', $p) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-pencil-check"></i> Input Nilai
                    </a>
                    @endif
                    @if($p->status === 'selesai')
                    <a href="{{ route('ranking.hasil', $p) }}" class="btn btn-success-soft btn-sm">
                        <i class="ti ti-trophy"></i> Hasil
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center py-5" style="color:#64748b">
            <i class="ti ti-calendar-off" style="font-size:32px;margin-bottom:8px;display:block"></i>
            Belum ada periode. <a href="{{ route('periode.create') }}">Buat periode baru</a>
        </div>
    </div>
    @endforelse

    <div class="col-md-4">
        <a href="{{ route('periode.create') }}" style="text-decoration:none">
            <div class="card h-100 text-center py-5" style="border-style:dashed;cursor:pointer;color:#64748b">
                <i class="ti ti-plus" style="font-size:28px;margin-bottom:6px;display:block"></i>
                <div style="font-size:11px">Buat Periode Baru</div>
            </div>
        </a>
    </div>
</div>
@endsection