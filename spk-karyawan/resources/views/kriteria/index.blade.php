@extends('layouts.app')
@section('title','Kriteria & Bobot')
@section('content')
@if($errors->any())
<div class="alert-spk al-warn" style="margin-bottom:12px">
    <i class="ti ti-alert-circle"></i>
    <div>
        <strong>Gagal menyimpan:</strong>
        <ul style="margin:4px 0 0 16px;padding:0">
            @foreach($errors->all() as $e)
            <li style="font-size:12px">{{ $e }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
<style>
.pb{background:#eef2f7;border-radius:6px;height:8px;overflow:hidden}
.pf{background:#2563eb;height:100%;border-radius:6px}
</style>

<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Kriteria &amp; Bobot</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Kriteria penilaian dipisahkan menurut jenis kepegawaian. Tiap set bobotnya harus tepat <strong>100%</strong> sebelum periode penilaian dapat dibuat.</div>
    </div>
</div>

@php
    $sets   = ['tetap' => $kriteriaTetap, 'tidak_tetap' => $kriteriaTidakTetap];
    $labels = ['tetap' => 'Karyawan Tetap', 'tidak_tetap' => 'Karyawan Tidak Tetap'];
@endphp

@foreach($sets as $tipe => $list)
@php
    $totalBobot = $list->sum('bobot');
    $kurang     = 100 - $totalBobot;
    $tbOk       = $totalBobot == 100;
@endphp
<div class="card" style="margin-bottom:24px">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <span style="font-size:16px;font-weight:700">
            <i class="ti ti-adjustments-horizontal"></i> Kriteria {{ $labels[$tipe] }}
            <span class="badge {{ $tipe=='tetap'?'bg-info-soft':'bg-warning-soft' }}" style="font-size:10px;margin-left:6px">{{ $list->count() }} kriteria</span>
        </span>
        <a href="{{ route('kriteria.create', ['tipe'=>$tipe]) }}" class="btn btn-primary btn-sm" style="font-size:12px;font-weight:600">
            <i class="ti ti-plus" style="color:#fff"></i> Tambah Kriteria
        </a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:16px 16px 0">
        <div style="background:#fff;border:1px solid #e2e8f0;border-left:3px solid #2563eb;border-radius:10px;padding:14px 16px">
            <div style="font-size:12px;color:#64748b;font-weight:600">Jumlah Kriteria</div>
            <div style="font-size:24px;font-weight:800;color:#1e293b;margin-top:2px">{{ $list->count() }}</div>
        </div>
        <div style="background:{{ $tbOk?'#f0fdf4':'#fef2f2' }};border:1px solid {{ $tbOk?'#bbf7d0':'#fecaca' }};border-left:3px solid {{ $tbOk?'#22c55e':'#ef4444' }};border-radius:10px;padding:14px 16px">
            <div style="font-size:12px;color:{{ $tbOk?'#15803d':'#b91c1c' }};font-weight:600">Total Bobot</div>
            <div style="font-size:24px;font-weight:800;color:{{ $tbOk?'#16a34a':'#dc2626' }};margin-top:2px">{{ $totalBobot }}%</div>
        </div>
        <div style="background:{{ $tbOk?'#fff':'#fef2f2' }};border:1px solid {{ $tbOk?'#e2e8f0':'#fecaca' }};border-left:3px solid {{ $tbOk?'#94a3b8':'#ef4444' }};border-radius:10px;padding:14px 16px">
            <div style="font-size:12px;color:{{ $tbOk?'#64748b':'#b91c1c' }};font-weight:600">{{ $kurang >= 0 ? 'Kekurangan' : 'Kelebihan' }} Bobot</div>
            <div style="font-size:24px;font-weight:800;color:{{ $tbOk?'#1e293b':'#dc2626' }};margin-top:2px">{{ abs($kurang) }}%</div>
        </div>
    </div>

    <div style="padding:14px 16px 0">
    @if($list->isEmpty())
        <div class="alert-spk al-warn" style="margin-bottom:12px"><i class="ti ti-alert-triangle"></i><span>Belum ada kriteria untuk {{ $labels[$tipe] }}. Tambahkan kriteria terlebih dahulu.</span></div>
    @elseif(!$tbOk)
        <div class="alert-spk al-warn" style="margin-bottom:12px"><i class="ti ti-alert-triangle"></i><span>Total bobot harus <strong>100%</strong>. Saat ini <strong>{{ $totalBobot }}%</strong> — {{ $kurang > 0 ? 'kurang '.$kurang.'%' : 'kelebihan '.abs($kurang).'%' }}. Sesuaikan bobot di bawah.</span></div>
    @else
        <div class="alert-spk al-ok" style="margin-bottom:12px"><i class="ti ti-check-circle"></i><span>Total bobot sudah <strong>100%</strong>. Periode penilaian untuk {{ $labels[$tipe] }} dapat dibuat.</span></div>
    @endif
    </div>

    <table class="table mb-0">
        <thead>
            <tr>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-tag"></i> Nama Kriteria</span></th>
                <th style="width:90px;color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-category"></i> Jenis</span></th>
                <th style="width:110px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-percentage"></i> Bobot</span></th>
                <th style="color:#475569;font-weight:700"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-chart-bar"></i> Distribusi</span></th>
                <th style="width:110px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-list-details"></i> Sub-Kriteria</span></th>
                <th style="width:80px;color:#475569;font-weight:700" class="text-center"><span style="display:inline-flex;align-items:center;gap:5px"><i class="ti ti-settings"></i> Aksi</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse($list as $k)
            <tr>
                <td style="font-weight:700;color:#1e293b;font-size:14px">{{ $k->nama }}</td>
                <td><span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}" style="font-size:12px;padding:4px 11px">{{ ucfirst($k->jenis) }}</span></td>
                <td class="text-center" style="font-weight:700;color:#185FA5">{{ $k->bobot }}%</td>
                <td style="min-width:120px">
                    <div class="pb"><div class="pf" style="width:{{ min($k->bobot,100) }}%"></div></div>
                </td>
                <td class="text-center">
                    <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-info-soft btn-sm">
                        <i class="ti ti-list-details"></i> {{ $k->sub_kriteria_count }} skala
                    </a>
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <a href="{{ route('kriteria.edit', $k->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="konfirmasiHapusKriteria('{{ route('kriteria.destroy', $k) }}', '{{ addslashes($k->nama) }}')">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4" style="color:#64748b">
                Belum ada kriteria untuk {{ $labels[$tipe] }}.
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach

{{-- Modal Konfirmasi Hapus --}}
<div id="modal-hapus-kriteria" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:380px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div style="width:40px;height:40px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-trash" style="color:#ef4444;font-size:20px"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:15px">Hapus Kriteria</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Sub-kriteria terkait juga akan terhapus</div>
            </div>
        </div>
        <div style="background:#fef2f2;border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:13px;color:#374151">
            Yakin ingin menghapus kriteria: <strong id="modal-nama-kriteria" style="color:#ef4444"></strong>?
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end">
            <button onclick="tutupModalKriteria()" class="btn btn-outline-secondary">Batal</button>
            <form id="form-hapus-kriteria" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#ef4444;border-color:#ef4444;color:#fff;padding:7px 16px">
                    <i class="ti ti-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
function konfirmasiHapusKriteria(url, nama) {
    document.getElementById('form-hapus-kriteria').action = url;
    document.getElementById('modal-nama-kriteria').textContent = nama;
    document.getElementById('modal-hapus-kriteria').style.display = 'flex';
}
function tutupModalKriteria() {
    document.getElementById('modal-hapus-kriteria').style.display = 'none';
}
document.getElementById('modal-hapus-kriteria').addEventListener('click', function(e) {
    if (e.target === this) tutupModalKriteria();
});
</script>
@endpush