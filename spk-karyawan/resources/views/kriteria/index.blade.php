@extends('layouts.app')
@section('title','Kriteria & Bobot')
@section('content')
<div class="ph">
    <div>
        <div class="ph-title">Kriteria & Bobot</div>
        <div class="ph-sub">Master kriteria penilaian global — perubahan tidak memengaruhi periode yang sudah selesai</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKriteria" onclick="resetModal()">
        <i class="ti ti-plus"></i> Tambah Kriteria
    </button>
</div>

@php $totalBobot = $kriteria->sum('bobot_default'); @endphp
<div class="alert-spk {{ $totalBobot == 100 ? 'al-ok' : 'al-warn' }}">
    <i class="ti ti-{{ $totalBobot == 100 ? 'check-circle' : 'alert-triangle' }}"></i>
    Total bobot saat ini: <strong>{{ $totalBobot }}%</strong>
    @if($totalBobot != 100) — harus tepat 100% sebelum digunakan di periode penilaian @endif
</div>

<div class="card">
    <div class="card-header">
        <span><i class="ti ti-adjustments-horizontal"></i> Daftar Kriteria</span>
        <span style="font-size:11px;font-weight:600;color:{{ $totalBobot==100?'#27500A':'#A32D2D' }}">Total Bobot: {{ $totalBobot }}%</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr><th>Nama Kriteria</th><th>Tipe</th><th class="text-center">Bobot Default</th><th>Distribusi</th><th class="text-center">Sub-Kriteria</th><th class="text-center">Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($kriteria as $k)
            <tr>
                <td style="font-weight:600">{{ $k->nama }}</td>
                <td><span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ $k->jenis }}</span></td>
                <td class="text-center" style="font-weight:600;color:#185FA5">{{ $k->bobot_default }}%</td>
                <td style="min-width:120px">
                    <div class="pb"><div class="pf" style="width:{{ min($k->bobot_default,100) }}%"></div></div>
                </td>
                <td class="text-center">
                    <a href="{{ route('kriteria.sub-kriteria', $k) }}" class="btn btn-info-soft btn-sm">
                        <i class="ti ti-list-details"></i> {{ $k->sub_kriteria_count }} skala
                    </a>
                </td>
                <td class="text-center">
                    <div style="display:flex;gap:5px;justify-content:center">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKriteria"
                            onclick="isiModal({{ $k->id }},'{{ addslashes($k->nama) }}','{{ $k->jenis }}',{{ $k->bobot_default }})">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <form action="{{ route('kriteria.destroy', $k) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus kriteria {{ $k->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4" style="color:#64748b">Belum ada kriteria.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalKriteria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border:0.5px solid #e2e8f0;border-radius:10px">
            <div class="modal-header" style="border-bottom:0.5px solid #e2e8f0">
                <h6 class="modal-title fw-bold" id="modal-title">Tambah Kriteria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-kriteria" method="POST" action="{{ route('kriteria.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe <span style="color:#ef4444">*</span></label>
                        <select name="jenis" id="inp-jenis" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="benefit">Benefit (semakin tinggi = semakin baik)</option>
                            <option value="cost">Cost (semakin rendah = semakin baik)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot Default (%) <span style="color:#ef4444">*</span></label>
                        <input type="number" name="bobot_default" id="inp-bobot" class="form-control" min="0" max="100" step="0.01" required>
                        <div style="font-size:10px;color:#64748b;margin-top:3px">Total semua kriteria harus = 100%</div>
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