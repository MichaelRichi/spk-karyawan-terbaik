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
.select-wrap { position:relative; }
.select-wrap::after {
    content:'';
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    width:0;
    height:0;
    border-left:5px solid transparent;
    border-right:5px solid transparent;
    border-top:6px solid #64748b;
    pointer-events:none;
}
.select-wrap select {
    appearance:none;
    -webkit-appearance:none;
    padding-right:32px;
    cursor:pointer;
}
#modalKriteria .form-label {
    font-weight:700;
    color:#1e293b;
    font-size:13px;
}
</style>
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Kriteria & Bobot</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Atur kriteria, bobot, dan sub-kriteria di sini sebelum membuat periode penilaian</div>
        </div>
        <a href="{{ route('kriteria.create') }}" class="btn btn-primary" style="padding:8px 14px;font-size:12px;font-weight:600">
            <i class="ti ti-plus"></i> Tambah Kriteria
        </a>
    </div>
</div>

@php $totalBobot = $kriteria->sum('bobot'); $kurang = 100 - $totalBobot; @endphp

{{-- Status bobot total --}}
<style>
.kr-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
.kr-stat{background:var(--cb,#fff);border:1px solid var(--brd,#e9eef5);border-radius:12px;padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:10px;border-left:4px solid var(--ac)}
.kr-stat-lbl{font-size:12px;color:#64748b;font-weight:600;margin-bottom:5px}
.kr-stat-val{font-size:24px;font-weight:800;line-height:1}
.kr-stat-ic{width:46px;height:46px;border-radius:12px;background:var(--icbg,var(--ac));display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kr-stat-ic i{font-size:23px;color:var(--ac)}
@media(max-width:640px){.kr-stats{grid-template-columns:1fr}}
</style>

@php
    $tbOk = $totalBobot == 100;
    $tbAc = $tbOk ? '#16a34a' : '#dc2626';  $tbCb = $tbOk ? '#dcfce7' : '#fde2e2';
    $kgOk = $kurang == 0;
    $kgAc = $kgOk ? '#16a34a' : '#dc2626';   $kgCb = $kgOk ? '#dcfce7' : '#fde2e2';
    $kgLbl = $kurang < 0 ? 'Kelebihan Bobot' : 'Kekurangan Bobot';
@endphp
<div class="kr-stats">
    <div class="kr-stat" style="--ac:#2563eb;--icbg:#dbeafe">
        <div>
            <div class="kr-stat-lbl">Jumlah Kriteria</div>
            <div class="kr-stat-val" style="color:#1e293b">{{ $kriteria->count() }}</div>
        </div>
        <div class="kr-stat-ic"><i class="ti ti-list-check"></i></div>
    </div>
    <div class="kr-stat" style="--ac:{{ $tbAc }};--cb:{{ $tbCb }};--brd:{{ $tbAc }};--icbg:#fff">
        <div>
            <div class="kr-stat-lbl" style="color:{{ $tbAc }}">Total Bobot</div>
            <div class="kr-stat-val" style="color:{{ $tbAc }}">{{ $totalBobot }}%</div>
        </div>
        <div class="kr-stat-ic"><i class="ti ti-percentage"></i></div>
    </div>
    <div class="kr-stat" style="--ac:{{ $kgAc }};--cb:{{ $kgCb }};--brd:{{ $kgAc }};--icbg:#fff">
        <div>
            <div class="kr-stat-lbl" style="color:{{ $kgAc }}">{{ $kgLbl }}</div>
            <div class="kr-stat-val" style="color:{{ $kgAc }}">{{ abs($kurang) }}%</div>
        </div>
        <div class="kr-stat-ic"><i class="ti ti-scale"></i></div>
    </div>
</div>

@if($totalBobot != 100)
<div class="alert-spk al-warn" style="margin-bottom:12px">
    <i class="ti ti-alert-triangle"></i>
    <span>Total bobot harus <strong>100%</strong> sebelum bisa membuat periode penilaian. Saat ini <strong>{{ $totalBobot }}%</strong> — {{ $kurang > 0 ? 'kurang '.$kurang.'%' : 'kelebihan '.abs($kurang).'%' }}. Sesuaikan bobot di bawah.</span>
</div>
@else
<div class="alert-spk al-ok" style="margin-bottom:12px">
    <i class="ti ti-check-circle"></i>
    <span>Total bobot sudah <strong>100%</strong>. Periode penilaian dapat dibuat.</span>
</div>
@endif

<div class="card">
    <div class="card-header">
        <span style="font-size:16px;font-weight:700"><i class="ti ti-adjustments-horizontal"></i> Daftar Kriteria</span>
        
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
            @forelse($kriteria as $k)
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
                Belum ada kriteria. Tambahkan kriteria terlebih dahulu.
            </td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc">
                <td colspan="2" style="font-weight:700;font-size:12px">Total Bobot</td>
                <td class="text-center" style="font-weight:700;color:{{ $totalBobot==100?'#27500A':'#ef4444' }}">{{ $totalBobot }}%</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</div>

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