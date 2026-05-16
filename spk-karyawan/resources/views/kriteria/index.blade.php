@extends('layouts.app')
@section('title','Kriteria & Bobot')
@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKriteria" onclick="resetModal()">
        <i class="ti ti-plus me-1"></i> Tambah Kriteria
    </button>
</div>

<div class="card border-0 shadow-sm">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr><th>#</th><th>Nama Kriteria</th><th>Jenis</th><th>Bobot Default</th><th>Sub-Kriteria</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($kriteria as $k)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $k->nama }}</td>
                <td><span class="badge bg-{{ $k->jenis=='benefit'?'success':'danger' }}">{{ $k->jenis }}</span></td>
                <td class="fw-semibold text-primary">{{ $k->bobot_default }}%</td>
                <td>
                    <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-sm btn-outline-info">
                        <i class="ti ti-list-details me-1"></i>{{ $k->sub_kriteria_count }} skala
                    </a>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalKriteria"
                        onclick="isiModal({{ $k->id }},'{{ addslashes($k->nama) }}','{{ $k->jenis }}',{{ $k->bobot_default }})">
                        <i class="ti ti-pencil"></i>
                    </button>
                    <form action="{{ route('kriteria.destroy',$k) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Hapus kriteria {{ $k->nama }}? Sub-kriterianya juga akan terhapus.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="ti ti-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada kriteria.</td></tr>
            @endforelse
        </tbody>
        @if($kriteria->count())
        <tfoot>
            <tr class="table-light">
                <td colspan="3" class="fw-semibold">Total bobot</td>
                <td class="fw-semibold {{ $kriteria->sum('bobot_default')==100?'text-success':'text-danger' }}">
                    {{ $kriteria->sum('bobot_default') }}%
                    @if($kriteria->sum('bobot_default') != 100)
                    <small>(harus 100%)</small>
                    @endif
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalKriteria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title">Tambah Kriteria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-kriteria" method="POST" action="{{ route('kriteria.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span class="text-danger">*</span></label>
                        <select name="jenis" id="inp-jenis" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="benefit">Benefit (semakin tinggi semakin baik)</option>
                            <option value="cost">Cost (semakin rendah semakin baik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot Default (%) <span class="text-danger">*</span></label>
                        <input type="number" name="bobot_default" id="inp-bobot" class="form-control"
                            min="0" max="100" step="0.01" required>
                        <div class="form-text">Total semua kriteria harus = 100%</div>
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
const storeUrl = '{{ route('kriteria.store') }}';
function resetModal() {
    document.getElementById('modal-title').textContent = 'Tambah Kriteria';
    document.getElementById('form-kriteria').action = storeUrl;
    document.getElementById('form-method').value = 'POST';
    document.getElementById('inp-nama').value = '';
    document.getElementById('inp-jenis').value = '';
    document.getElementById('inp-bobot').value = '';
}
function isiModal(id, nama, jenis, bobot) {
    document.getElementById('modal-title').textContent = 'Edit Kriteria';
    document.getElementById('form-kriteria').action = '/kriteria/' + id;
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('inp-nama').value = nama;
    document.getElementById('inp-jenis').value = jenis;
    document.getElementById('inp-bobot').value = bobot;
}
</script>
@endpush