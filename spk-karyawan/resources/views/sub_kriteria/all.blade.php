@extends('layouts.app')
@section('title','Sub-Kriteria')
@section('content')

<div class="card" style="max-width:700px;margin:0 auto 16px">
    <div style="padding:20px 24px">
        <div style="font-size:18px;font-weight:800;color:#1e293b">Sub-Kriteria</div>
        <div style="font-size:12px;color:#64748b;margin-top:2px">Semua skala penilaian dari seluruh kriteria</div>
    </div>
</div>

@foreach($kriteria as $k)
<div class="card" style="margin-bottom:16px;max-width:700px;margin-left:auto;margin-right:auto">

    {{-- Header kriteria --}}
    <div class="card-header" style="padding:14px 16px">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:36px;height:36px;border-radius:8px;background:{{ $k->jenis=='benefit'?'#dcfce7':'#fee2e2' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-chart-line" style="color:{{ $k->jenis=='benefit'?'#16a34a':'#dc2626' }};font-size:16px"></i>
            </div>
            <div>
                <div style="font-size:15px;font-weight:700;color:#1e293b">{{ $k->nama }}</div>
                <div style="display:flex;gap:6px;margin-top:3px">
                    <span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ ucfirst($k->jenis) }}</span>
                    <span class="badge bg-gray-soft">Bobot {{ $k->bobot }}%</span>
                </div>
            </div>
        </div>
        <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-primary btn-sm">
            <i class="ti ti-edit" style="color:#fff"></i> Kelola Sub-Kriteria
        </a>
    </div>

    {{-- Tabel --}}
    <div style="border-top:2px solid #e2e8f0">
        <div style="display:flex;gap:14px;padding:8px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0">
            <div style="width:32px;flex-shrink:0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;text-align:center">Skor</div>
            <div style="flex:1;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase">Deskripsi</div>
            <div style="width:74px;flex-shrink:0;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;text-align:center">Aksi</div>
        </div>
        @forelse($k->jenis === 'cost' ? $k->subKriteria->sortBy('skor') : $k->subKriteria->sortByDesc('skor') as $sk)
        <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9' : '' }}">
            {{-- Skor --}}
            <div style="width:32px;height:32px;border-radius:6px;background:#2563eb;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                {{ $sk->skor }}
            </div>
            {{-- Deskripsi --}}
            <div style="flex:1;font-size:12px;font-weight:600;color:#1e293b">
                {{ $sk->nama }}
            </div>
            {{-- Aksi --}}
            <div style="display:flex;gap:6px;flex-shrink:0">
                <a href="{{ route('kriteria.sub-kriteria.edit', [$k->id, $sk->id]) }}" class="btn btn-sm" style="background:#2563eb;border-color:#2563eb;color:#fff">
                    <i class="ti ti-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-sm"
                    style="background:#ef4444;border-color:#ef4444;color:#fff"
                    onclick="konfirmasiHapus('{{ route('kriteria.sub-kriteria.destroy', [$k, $sk]) }}', '{{ addslashes($sk->nama) }}')">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:20px;color:#64748b;font-size:13px">
            Belum ada skala. <a href="{{ route('kriteria.sub-kriteria', $k) }}">Tambah sekarang</a>
        </div>
        @endforelse
    </div>
</div>
@endforeach


{{-- Modal Konfirmasi Hapus --}}
<div id="modal-hapus" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:380px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
            <div style="width:40px;height:40px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti ti-trash" style="color:#ef4444;font-size:20px"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#1e293b;font-size:15px">Hapus Sub-Kriteria</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px">Tindakan ini tidak dapat dibatalkan</div>
            </div>
        </div>
        <div style="background:#fef2f2;border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:13px;color:#374151">
            Yakin ingin menghapus: <strong id="modal-nama" style="color:#ef4444"></strong>?
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end">
            <button onclick="tutupModal()" class="btn btn-outline-secondary">Batal</button>
            <form id="form-hapus" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:#ef4444;border-color:#ef4444;color:#fff;padding:7px 16px">
                    <i class="ti ti-trash"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function konfirmasiHapus(url, nama) {
    document.getElementById('form-hapus').action = url;
    document.getElementById('modal-nama').textContent = nama;
    document.getElementById('modal-hapus').style.display = 'flex';
}
function tutupModal() {
    document.getElementById('modal-hapus').style.display = 'none';
}
document.getElementById('modal-hapus').addEventListener('click', function(e) {
    if (e.target === this) tutupModal();
});
</script>
@endpush

@endsection