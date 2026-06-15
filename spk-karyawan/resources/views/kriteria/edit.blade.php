@extends('layouts.app')
@section('title','Ubah Kriteria')
@section('content')

<style>
.kr-form .form-label{font-weight:700;color:#1e293b;font-size:13px;margin-bottom:5px}
.kr-form .form-control,.kr-form .form-select{font-size:14px;padding:9px 12px}
.kr-form .form-select{padding-right:32px}
</style>

<div class="card" style="max-width:560px;margin:0 auto 16px">
    <div style="padding:20px 24px;display:flex;align-items:center;gap:14px">
        <div style="width:46px;height:46px;border-radius:12px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="ti ti-pencil" style="font-size:23px;color:#d97706"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:800;color:#1e293b">Ubah Kriteria</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $kriteria->nama }}</div>
        </div>
    </div>
</div>

<div style="max-width:560px;margin:0 auto">
    <div class="card">
        <div style="padding:20px 22px">
            <form method="POST" action="{{ route('kriteria.update', $kriteria->id) }}" class="kr-form" onsubmit="prosesSatuan()">
                @csrf
                @method('PUT')
                <input type="hidden" name="satuan_rentang" id="inp-satuan-final">

                <div class="mb-3">
                    <label class="form-label">Nama Kriteria <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                        value="{{ old('nama', $kriteria->nama) }}" placeholder="Contoh: Kehadiran" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis <span style="color:#ef4444">*</span></label>
                    <div style="position:relative">
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required
                        style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                        <option value="">-- Pilih --</option>
                        <option value="benefit" {{ old('jenis', $kriteria->jenis)=='benefit'?'selected':'' }}>Benefit — semakin tinggi semakin baik</option>
                        <option value="cost" {{ old('jenis', $kriteria->jenis)=='cost'?'selected':'' }}>Cost — semakin rendah semakin baik</option>
                    </select>
                    <i class="ti ti-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:#64748b;font-size:13px"></i>
                    </div>
                    @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Bobot (%) <span style="color:#ef4444">*</span></label>
                    <input type="number" name="bobot" class="form-control @error('bobot') is-invalid @enderror"
                        min="1" max="100" step="1" required placeholder="Contoh: 30" value="{{ old('bobot', $kriteria->bobot) }}">
                    <div style="font-size:11px;color:#64748b;margin-top:4px">
                        Total bobot semua kriteria harus = 100%.
                        Sisa bobot (di luar kriteria ini): <strong>{{ 100 - $totalBobot }}%</strong>
                    </div>
                    @error('bobot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Opsi Rentang --}}
                <div style="background:#f8fafc;border-radius:8px;padding:12px;border:0.5px solid #e2e8f0;margin-bottom:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                        <input type="checkbox" name="has_rentang" id="inp-has-rentang" value="1"
                            onchange="toggleRentang(this.checked)" {{ old('has_rentang', $kriteria->has_rentang) ? 'checked' : '' }}
                            style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer">
                        <label for="inp-has-rentang" style="font-size:14px;font-weight:700;color:#1e293b;cursor:pointer;margin:0">
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
                                style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
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

                <div style="display:flex;gap:10px">
                    <a href="{{ route('kriteria.index') }}" class="btn" style="flex:1;justify-content:center;font-size:14px;padding:10px;background:#e2e8f0;border:1.5px solid #94a3b8;color:#1e293b;font-weight:700">Batal</a>
                    <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;font-size:14px;padding:10px;font-weight:600">
                        <i class="ti ti-device-floppy"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
const SATUAN_AWAL = @json($kriteria->satuan_rentang);
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
// Inisialisasi nilai rentang saat halaman dibuka
(function() {
    var hasR = document.getElementById('inp-has-rentang').checked;
    toggleRentang(hasR);
    if (hasR && SATUAN_AWAL) {
        var baku = ['hari','tahun','kali'];
        if (baku.indexOf(SATUAN_AWAL) === -1) {
            document.getElementById('inp-satuan').value = 'lainnya';
            document.getElementById('inp-satuan-manual').value = SATUAN_AWAL;
            toggleSatuanLain('lainnya');
        } else {
            document.getElementById('inp-satuan').value = SATUAN_AWAL;
        }
    }
})();
</script>
@endpush