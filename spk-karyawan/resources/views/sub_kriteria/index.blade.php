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
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSk" onclick="resetModal()">
                <i class="ti ti-plus"></i> Tambah Sub-Kriteria
            </button>
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

<!-- Modal -->
<div class="modal fade" id="modalSk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border:0.5px solid #e2e8f0;border-radius:10px">
            <div class="modal-header" style="border-bottom:0.5px solid #e2e8f0">
                <h6 class="modal-title fw-bold" id="modal-title">Tambah Sub-Kriteria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sk-form" method="POST" action="{{ route('kriteria.sub-kriteria', $kriteria) }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:700;color:#1e293b;font-size:13px">Skor <span style="color:#ef4444">*</span></label>
                        <input type="number" name="skor" id="inp-skor" class="form-control" min="1" max="10" required>
                        <div style="font-size:10px;color:#64748b;margin-top:3px">Nilai numerik 1–10. Tidak boleh duplikat dalam satu kriteria.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:700;color:#1e293b;font-size:13px">Deskripsi <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control" placeholder="Contoh: ≥ 26 hari, Sangat Baik" required>
                    @if($kriteria->has_rentang)
                    <div class="mt-3">
                        <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:6px">
                            Rentang Tahun Kerja <span style="font-size:10px;color:#64748b;font-weight:400">(untuk pengisian otomatis)</span>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center">
                            <div style="flex:1">
                                <label style="font-size:12px;color:#475569;font-weight:600;margin-bottom:3px;display:block">Min ({{ $kriteria->satuan_rentang ?? 'angka' }})</label>
                                <input type="number" name="nilai_min" id="inp-nilai-min"
                                    class="form-control" step="0.01" min="0" placeholder="0">
                            </div>
                            <div style="color:#94a3b8;margin-top:16px">—</div>
                            <div style="flex:1">
                                <label style="font-size:12px;color:#475569;font-weight:600;margin-bottom:3px;display:block">Max ({{ $kriteria->satuan_rentang ?? 'angka' }})</label>
                                <input type="number" name="nilai_max" id="inp-nilai-max"
                                    class="form-control" step="0.01" min="0" placeholder="99">
                            </div>
                        </div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:4px">
                            Isi 0 dan 0 untuk "kurang dari 1 tahun"
                        </div>
                    </div>
                    @endif
                    </div>

                </div>
                <div class="modal-footer" style="border-top:0.5px solid #e2e8f0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const baseUrl = '{{ route('kriteria.sub-kriteria', $kriteria) }}';
function resetModal() {
    document.getElementById('modal-title').textContent = 'Tambah Sub-Kriteria';
    document.getElementById('sk-form').action = baseUrl;
    document.getElementById('form-method').value = 'POST';
    document.getElementById('inp-skor').value = '';
    document.getElementById('inp-nama').value = '';
}
function isiModal(id, nama, skor, nilaiMin, nilaiMax) {
    document.getElementById('modal-title').textContent = 'Edit Sub-Kriteria';
    document.getElementById('sk-form').action = baseUrl + '/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('inp-skor').value = skor;
    if (document.getElementById('inp-nilai-min')) document.getElementById('inp-nilai-min').value = nilaiMin || '';
    if (document.getElementById('inp-nilai-max')) document.getElementById('inp-nilai-max').value = nilaiMax || '';
    document.getElementById('inp-nama').value = nama;
}
</script>
@endpush