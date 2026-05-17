@extends('layouts.app')
@section('title','Sub-Kriteria — '.$kriteria->nama)
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Sub-Kriteria — {{ $kriteria->nama }}</div>
        <div class="ph-sub">
            <span class="badge {{ $kriteria->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ $kriteria->jenis }}</span>
            &nbsp;Bobot default: <strong>{{ $kriteria->bobot_default }}%</strong>
        </div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('sub-kriteria.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSk" onclick="resetModal()">
            <i class="ti ti-plus"></i> Tambah Skala
        </button>
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
        <span><i class="ti ti-list-details"></i> Skala Penilaian</span>
        <span class="badge bg-gray-soft">{{ $kriteria->subKriteria->count() }} skala</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th class="text-center" style="width:80px">Skor</th><th>Label</th><th>Keterangan</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($kriteria->subKriteria->sortByDesc('skor') as $sk)
            <tr>
                <td class="text-center">
                    <span class="badge bg-primary" style="font-size:13px;padding:4px 10px">{{ $sk->skor }}</span>
                </td>
                <td style="font-weight:600">{{ $sk->nama }}</td>
                <td style="color:#64748b;font-size:13px">{{ $sk->keterangan ?? '—' }}</td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalSk"
                            onclick="isiModal({{ $sk->id }},'{{ addslashes($sk->nama) }}',{{ $sk->skor }},'{{ addslashes($sk->keterangan ?? '') }}')">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <form action="{{ route('kriteria.sub-kriteria.destroy', [$kriteria, $sk]) }}"
                            method="POST" class="d-inline" onsubmit="return confirm('Hapus skala ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></button>
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
                <h6 class="modal-title fw-bold" id="modal-title">Tambah Skala Penilaian</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sk-form" method="POST" action="{{ route('kriteria.sub-kriteria', $kriteria) }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skor <span style="color:#ef4444">*</span></label>
                        <input type="number" name="skor" id="inp-skor" class="form-control" min="1" max="10" required>
                        <div style="font-size:10px;color:#64748b;margin-top:3px">Nilai numerik 1–10. Tidak boleh duplikat dalam satu kriteria.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Label <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control" placeholder="Contoh: ≥ 26 hari, Sangat Baik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan <span style="color:#94a3b8">(opsional)</span></label>
                        <input type="text" name="keterangan" id="inp-keterangan" class="form-control" placeholder="Contoh: Hadir lebih dari 26 hari dalam sebulan">
                        <div style="font-size:10px;color:#64748b;margin-top:3px">Penjelasan detail yang ditampilkan saat input penilaian.</div>
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
    document.getElementById('modal-title').textContent = 'Tambah Skala Penilaian';
    document.getElementById('sk-form').action = baseUrl;
    document.getElementById('form-method').value = 'POST';
    document.getElementById('inp-skor').value = '';
    document.getElementById('inp-nama').value = '';
    document.getElementById('inp-keterangan').value = '';
}
function isiModal(id, nama, skor, keterangan) {
    document.getElementById('modal-title').textContent = 'Edit Skala Penilaian';
    document.getElementById('sk-form').action = baseUrl + '/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('inp-skor').value = skor;
    document.getElementById('inp-nama').value = nama;
    document.getElementById('inp-keterangan').value = keterangan || '';
}
</script>
@endpush