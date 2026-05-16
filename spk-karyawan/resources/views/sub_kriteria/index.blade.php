@extends('layouts.app')
@section('title','Sub-Kriteria — '.$kriteria->nama)
@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('kriteria.index') }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left"></i></a>
    <div>
        <span class="fw-semibold">{{ $kriteria->nama }}</span>
        <span class="badge bg-{{ $kriteria->jenis=='benefit'?'success':'danger' }} ms-1">{{ $kriteria->jenis }}</span>
        <span class="badge bg-secondary ms-1">Bobot: {{ $kriteria->bobot_default }}%</span>
    </div>
    <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalSk" onclick="resetModal()">
        <i class="ti ti-plus me-1"></i> Tambah Skala
    </button>
</div>

<div class="card border-0 shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr><th class="text-center" style="width:80px">Skor</th><th>Nama</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($kriteria->subKriteria->sortByDesc('skor') as $sk)
            <tr>
                <td class="text-center"><span class="badge bg-primary fs-6">{{ $sk->skor }}</span></td>
                <td class="fw-semibold">{{ $sk->nama }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalSk"
                        onclick="isiModal({{ $sk->id }},'{{ addslashes($sk->nama) }}',{{ $sk->skor }})">
                        <i class="ti ti-pencil"></i>
                    </button>
                    <form action="{{ route('kriteria.sub-kriteria.destroy',[$kriteria,$sk]) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Hapus skala ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada skala penilaian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="alert alert-{{ $kriteria->jenis=='benefit'?'success':'info' }} mt-3 small">
    @if($kriteria->jenis=='benefit')
    <i class="ti ti-info-circle me-1"></i><strong>Benefit:</strong> Skor tertinggi = terbaik. Rumus: r = x / Max(x)
    @else
    <i class="ti ti-info-circle me-1"></i><strong>Cost:</strong> Skor terendah = terbaik. Rumus: r = Min(x) / x
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="modalSk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title">Tambah Skala Penilaian</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-sk" method="POST" action="{{ route('kriteria.sub-kriteria',$kriteria) }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Skor <span class="text-danger">*</span></label>
                        <input type="number" name="skor" id="inp-skor" class="form-control" min="1" max="10" required>
                        <div class="form-text">Nilai numerik 1–10. Tidak boleh duplikat.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control"
                            placeholder="Contoh: ≥ 26 hari" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const baseUrl = '{{ route('kriteria.sub-kriteria',$kriteria) }}';
function resetModal() {
    document.getElementById('modal-title').textContent = 'Tambah Skala Penilaian';
    document.getElementById('form-sk').action = baseUrl;
    document.getElementById('form-method').value = 'POST';
    document.getElementById('inp-skor').value = '';
    document.getElementById('inp-nama').value = '';
}
function isiModal(id, nama, skor) {
    document.getElementById('modal-title').textContent = 'Edit Skala Penilaian';
    document.getElementById('form-sk').action = baseUrl + '/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('inp-skor').value = skor;
    document.getElementById('inp-nama').value = nama;
}
</script>
@endpush