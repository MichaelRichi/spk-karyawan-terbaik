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
</style>
<div class="card" style="margin-bottom:16px">
    <div style="padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Kriteria & Bobot</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">Atur kriteria, bobot, dan sub-kriteria di sini sebelum membuat periode penilaian</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalKriteria" onclick="resetModal()">
            <i class="ti ti-plus"></i> Tambah Kriteria
        </button>
    </div>
</div>

@php $totalBobot = $kriteria->sum('bobot'); $kurang = 100 - $totalBobot; @endphp

{{-- Status bobot total --}}
<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:12px">
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-adjustments-horizontal"></i> Jumlah Kriteria</div>
        <div class="stat-val" style="font-size:22px">{{ $kriteria->count() }}</div>
    </div>
    <div class="stat-card" style="{{ $totalBobot==100?'border-color:#97C459;background:#EAF3DE':'border-color:#f59e0b;background:#FAEEDA' }}">
        <div class="stat-lbl" style="{{ $totalBobot==100?'color:#3B6D11':'color:#854F0B' }}">Total bobot</div>
        <div class="stat-val" style="font-size:22px;{{ $totalBobot==100?'color:#27500A':'color:#633806' }}">{{ $totalBobot }}%</div>
    </div>
    <div class="stat-card" style="{{ $kurang==0?'border-color:#97C459;background:#EAF3DE':($kurang<0?'border-color:#fca5a5;background:#FCEBEB':'border-color:#fca5a5;background:#FCEBEB') }}">
        <div class="stat-lbl" style="{{ $kurang==0?'color:#3B6D11':'color:#791F1F' }}">{{ $kurang < 0 ? 'Kelebihan' : 'Kekurangan' }} bobot</div>
        <div class="stat-val" style="font-size:22px;{{ $kurang==0?'color:#27500A':'color:#ef4444' }}">{{ abs($kurang) }}%</div>
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
        <span><i class="ti ti-adjustments-horizontal"></i> Daftar Kriteria</span>
        <span style="font-size:11px;color:#64748b">Perubahan hanya berlaku untuk periode yang dibuat setelah ini</span>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Nama Kriteria</th>
                <th style="width:90px">Jenis</th>
                <th style="width:100px" class="text-center">Bobot (%)</th>
                <th>Distribusi</th>
                <th style="width:100px" class="text-center">Sub-Kriteria</th>
                <th style="width:80px" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kriteria as $k)
            <tr>
                <td style="font-weight:600">{{ $k->nama }}</td>
                <td><span class="badge {{ $k->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">{{ ucfirst($k->jenis) }}</span></td>
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
                        <button class="btn btn-outline-primary btn-sm"
                            onclick="isiModal({{ $k->id }},'{{ addslashes($k->nama) }}','{{ $k->jenis }}',{{ $k->bobot }},{{ $k->has_rentang ? 1 : 0 }},'{{ $k->satuan_rentang }}')">
                            <i class="ti ti-pencil"></i>
                        </button>
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

{{-- Modal Tambah/Edit Kriteria --}}
<div class="modal fade" id="modalKriteria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:0.5px solid #e2e8f0;border-radius:10px">
            <div class="modal-header" style="border-bottom:0.5px solid #e2e8f0">
                <h6 class="modal-title fw-bold" id="modal-title">Tambah Kriteria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-kriteria" method="POST" action="{{ route('kriteria.store') }}" onsubmit="prosesSatuan()">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="satuan_rentang" id="inp-satuan-final">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria <span style="color:#ef4444">*</span></label>
                        <input type="text" name="nama" id="inp-nama" class="form-control" placeholder="Contoh: Kehadiran" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis <span style="color:#ef4444">*</span></label>
                        <div class="select-wrap"><select name="jenis" id="inp-jenis" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="benefit">Benefit — semakin tinggi semakin baik</option>
                            <option value="cost">Cost — semakin rendah semakin baik</option>
                        </select></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot (%) <span style="color:#ef4444">*</span></label>
                        <input type="number" name="bobot" id="inp-bobot" class="form-control"
                            min="1" max="100" step="1" required placeholder="Contoh: 30">
                        <div style="font-size:11px;color:#64748b;margin-top:4px">
                            Total bobot semua kriteria harus = 100%.
                            Sisa bobot saat ini: <strong id="sisa-bobot">{{ 100 - $totalBobot }}%</strong>
                        </div>
                    </div>
                    {{-- Opsi Rentang --}}
                    <div style="background:#f8fafc;border-radius:8px;padding:12px;border:0.5px solid #e2e8f0;margin-top:12px">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                            <input type="checkbox" name="has_rentang" id="inp-has-rentang" value="1"
                                onchange="toggleRentang(this.checked)"
                                style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer">
                            <label for="inp-has-rentang" style="font-size:13px;font-weight:600;color:#374151;cursor:pointer;margin:0">
                                Gunakan Rentang Angka
                            </label>
                        </div>
                        <div style="font-size:11px;color:#64748b;margin-bottom:8px">
                            Aktifkan jika penilaian berdasarkan angka (contoh: hari hadir, tahun kerja)
                        </div>
                        <div id="div-satuan" style="display:none">
                            <label class="form-label" style="font-size:12px">Satuan <span style="color:#ef4444">*</span></label>
                            <div style="position:relative">
                                <select id="inp-satuan" class="form-select form-select-sm" onchange="toggleSatuanLain(this.value)"
                                    style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer"
                                   >
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="hari">Hari (otomatis dari jumlah hadir)</option>
                                    <option value="tahun">Tahun (otomatis dari masa kerja)</option>
                                    <option value="kali">Keterlambatan (otomatis dari jumlah terlambat)</option>
                                    <option value="lainnya">Lainnya (ketik manual)</option>
                                </select>
                                <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                            </div>
                            <div id="div-satuan-manual" style="display:none;margin-top:6px">
                                <input type="text" id="inp-satuan-manual" class="form-control form-control-sm"
                                    placeholder="Ketik satuan, contoh: unit, poin, km">
                            </div>
                        </div>
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
const storeUrl    = '{{ route('kriteria.store') }}';
const totalBobot  = {{ $totalBobot }};

function prosesSatuan() {
    var final = '';
    if (document.getElementById('inp-has-rentang').checked) {
        var sel = document.getElementById('inp-satuan').value;
        if (sel === 'lainnya') {
            sel = (document.getElementById('inp-satuan-manual').value || '');
        }
        final = sel.trim().toLowerCase();
    }
    document.getElementById('inp-satuan-final').value = final;
}
function resetModal() {
    document.getElementById('modal-title').textContent = 'Tambah Kriteria';
    document.getElementById('inp-has-rentang').checked = false;
    document.getElementById('div-satuan').style.display = 'none';
    document.getElementById('inp-satuan').value = '';
    document.getElementById('form-kriteria').action    = storeUrl;
    document.getElementById('form-method').value  = 'POST';
    document.getElementById('inp-nama').value     = '';
    document.getElementById('inp-jenis').value    = '';
    document.getElementById('inp-bobot').value    = '';
    document.getElementById('inp-has-rentang').checked = false;
    document.getElementById('inp-satuan').value = '';
    document.getElementById('div-satuan').style.display = 'none';
    document.getElementById('sisa-bobot').textContent = (100 - totalBobot) + '%';
}

function toggleSatuanLain(val) {
    document.getElementById('div-satuan-manual').style.display = val === 'lainnya' ? 'block' : 'none';
}
function toggleRentang(checked) {
    document.getElementById('div-satuan').style.display = checked ? 'block' : 'none';
    if (!checked) {
        document.getElementById('inp-satuan').value = '';
        document.getElementById('div-satuan-manual').style.display = 'none';
        document.getElementById('inp-satuan-manual').value = '';
    }
}
function isiModal(id, nama, jenis, bobot, hasRentang, satuan) {
    document.getElementById('modal-title').textContent = 'Edit Kriteria';
    document.getElementById('form-kriteria').action    = '{{ url('kriteria') }}/' + id;
    document.getElementById('form-method').value  = 'PUT';
    document.getElementById('inp-nama').value     = nama;
    document.getElementById('inp-jenis').value    = jenis;
    document.getElementById('inp-bobot').value    = bobot;
    const hasR = hasRentang == 1;
    document.getElementById('inp-has-rentang').checked = hasR;
    toggleRentang(hasR);
    const baku = ['hari','tahun','kali'];
    if (satuan && baku.indexOf(satuan) === -1) {
        document.getElementById('inp-satuan').value = 'lainnya';
        document.getElementById('inp-satuan-manual').value = satuan;
        toggleSatuanLain('lainnya');
    } else {
        document.getElementById('inp-satuan').value = satuan || '';
        toggleSatuanLain(satuan || '');
    }
    document.getElementById('sisa-bobot').textContent = (100 - totalBobot + bobot) + '%';
    // Buka modal setelah semua data terisi
    new bootstrap.Modal(document.getElementById('modalKriteria')).show();
}

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