@extends('layouts.app')
@section('title','Edit Nilai')
@section('content')

@php
$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$labelBulan = ($namaBulan[$periode->bulan] ?? $periode->bulan).' '.$periode->tahun;
@endphp

<div class="ph">
    <div>
        <div class="ph-title">Edit Nilai</div>
        <div class="ph-sub">Periode {{ $labelBulan }} · Perubahan akan menghitung ulang ranking</div>
    </div>
    <a href="{{ route('ranking.hasil', $periode) }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

{{-- Info karyawan --}}
<div class="card" style="margin-bottom:12px">
    <div class="card-header"><i class="ti ti-user"></i> Informasi Karyawan</div>
    <div style="padding:14px 16px;display:flex;gap:16px;flex-wrap:wrap">
        <div>
            <div style="font-size:10px;color:#64748b">Nama</div>
            <div style="font-weight:600;color:#1e293b">{{ $karyawan->nama }}</div>
        </div>
        <div>
            <div style="font-size:10px;color:#64748b">Divisi</div>
            <div style="font-weight:600;color:#1e293b">{{ $karyawan->divisi }}</div>
        </div>
        <div>
            <div style="font-size:10px;color:#64748b">Periode</div>
            <div style="font-weight:600;color:#1d4ed8">{{ $labelBulan }}</div>
        </div>
    </div>
</div>

{{-- Form edit nilai --}}
<div class="card">
    <div class="card-header">
        <span><i class="ti ti-edit"></i> Koreksi Nilai Per Kriteria</span>
    </div>
    <div style="padding:16px">
        @if($errors->any())
        <div class="alert-spk al-warn" style="margin-bottom:14px">
            <i class="ti ti-alert-circle"></i>
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('ranking.update-nilai', [$periode, $karyawan]) }}">
            @csrf @method('PUT')

            @foreach($periodeKriteria as $pk)
            @php $nilaiSaat = $nilaiExisting[$pk->id] ?? null; @endphp
            <div style="margin-bottom:16px;padding:14px;border:1px solid #e2e8f0;border-radius:8px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                    <div>
                        <div style="font-weight:600;color:#1e293b;font-size:13px">{{ $pk->nama_kriteria }}</div>
                        <div style="font-size:11px;color:#64748b">
                            {{ ucfirst($pk->jenis) }} · Bobot {{ $pk->bobot }}%
                        </div>
                    </div>
                    @if($nilaiSaat)
                    <span style="font-size:11px;background:#f1f5f9;padding:3px 8px;border-radius:6px;color:#374151">
                        Nilai saat ini: <strong>{{ $nilaiSaat->nilai }}</strong>
                    </span>
                    @endif
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    @foreach($pk->periodeSubKriteria->sortByDesc('skor') as $psk)
                    <label style="cursor:pointer">
                        <input type="radio" name="penilaian[{{ $pk->id }}]"
                            value="{{ $psk->id }}"
                            {{ $nilaiSaat && $nilaiSaat->periode_sub_kriteria_id == $psk->id ? 'checked' : '' }}
                            required style="display:none" class="radio-psk">
                        <div class="psk-card" style="border:2px solid #e2e8f0;border-radius:8px;padding:8px 12px;text-align:center;min-width:80px;transition:all .15s;
                            {{ $nilaiSaat && $nilaiSaat->periode_sub_kriteria_id == $psk->id ? 'border-color:#2563eb;background:#eff6ff;' : '' }}">
                            <div style="font-size:18px;font-weight:700;color:#1e293b">{{ $psk->skor }}</div>
                            <div style="font-size:10px;color:#64748b;margin-top:2px">{{ $psk->nama }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div style="display:flex;gap:8px;margin-top:8px">
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Simpan perubahan dan hitung ulang ranking?')">
                    <i class="ti ti-refresh"></i> Simpan & Hitung Ulang
                </button>
                <a href="{{ route('ranking.hasil', $periode) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.radio-psk').forEach(radio => {
    radio.addEventListener('change', function() {
        const name = this.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
            r.nextElementSibling.style.borderColor = '#e2e8f0';
            r.nextElementSibling.style.background = '';
        });
        if (this.checked) {
            this.nextElementSibling.style.borderColor = '#2563eb';
            this.nextElementSibling.style.background = '#eff6ff';
        }
    });
});
</script>
@endpush
@endsection