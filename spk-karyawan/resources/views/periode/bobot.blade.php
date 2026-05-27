@extends('layouts.app')
@section('title','Atur Bobot — '.$periode->bulan.'/'.$periode->tahun)
@section('content')

@php
    $total    = $periodeKriteria->sum('bobot');
    $kurang   = 100 - $total;
    $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $labelBulan = ($namaBulan[$periode->bulan] ?? $periode->bulan).' '.$periode->tahun;
@endphp

{{-- Page Header --}}
<div class="ph">
    <div>
        <div class="ph-title">{{ $labelBulan }} — atur bobot</div>
    </div>
    <a href="{{ route('periode.show', $periode) }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

{{-- Step Bar --}}
<div class="steps">
    <div class="step done"><i class="ti ti-check"></i> Buat periode</div>
    <div class="step-arr"><i class="ti ti-chevron-right"></i></div>
    <div class="step now"><i class="ti ti-sliders"></i> Atur bobot</div>
    <div class="step-arr"><i class="ti ti-chevron-right"></i></div>
    <div class="step"><i class="ti ti-pencil"></i> Input nilai</div>
    <div class="step-arr"><i class="ti ti-chevron-right"></i></div>
    <div class="step"><i class="ti ti-calculator"></i> Hitung Penilaian</div>
    <div class="step-arr"><i class="ti ti-chevron-right"></i></div>
    <div class="step"><i class="ti ti-lock"></i> Selesai</div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);max-width:560px;margin-bottom:12px">
    <div class="stat-card">
        <div class="stat-lbl"><i class="ti ti-calendar"></i> Periode</div>
        <div class="stat-val" style="font-size:15px;margin-top:4px">{{ $labelBulan }}</div>
    </div>
    <div class="stat-card" id="card-total" style="{{ $total==100?'border-color:#97C459;background:#EAF3DE':'border-color:#f59e0b;background:#FAEEDA' }}">
        <div class="stat-lbl" id="lbl-total" style="{{ $total==100?'color:#3B6D11':'color:#854F0B' }}">Total bobot saat ini</div>
        <div class="stat-val" id="val-total" style="font-size:22px;{{ $total==100?'color:#27500A':'color:#633806' }}">{{ $total }}%</div>
    </div>
    <div class="stat-card" id="card-kurang" style="{{ $kurang==0?'border-color:#97C459;background:#EAF3DE':'border-color:#fca5a5;background:#FCEBEB' }}">
        <div class="stat-lbl" id="lbl-kurang" style="{{ $kurang==0?'color:#3B6D11':'color:#791F1F' }}">Kekurangan bobot</div>
        <div class="stat-val" id="val-kurang" style="font-size:22px;{{ $kurang==0?'color:#27500A':'color:#ef4444' }}">{{ $kurang }}%</div>
    </div>
</div>

{{-- Warning jika belum 100% --}}
@if($total != 100)
<div class="alert-spk al-warn" style="max-width:760px">
    <i class="ti ti-alert-triangle"></i>
    <span>Total bobot kriteria belum mencapai 100%. Sesuaikan bobot di bawah hingga totalnya tepat 100% sebelum mengaktifkan periode ini. Perubahan bobot di sini tidak memengaruhi periode lain.</span>
</div>
@endif

{{-- Tabel Bobot --}}
<div class="card" style="max-width:760px">
    <div class="card-header" style="justify-content:space-between">
        <span><i class="ti ti-sliders"></i> Atur bobot kriteria — khusus periode {{ $labelBulan }}</span>
        <button type="submit" form="form-bobot" class="btn btn-primary btn-sm">
            <i class="ti ti-device-floppy"></i> Simpan bobot
        </button>
    </div>

    <form id="form-bobot" method="POST" action="{{ route('periode.bobot.update', $periode) }}">
        @csrf @method('PUT')
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width:50px">Kode</th>
                    <th>Nama kriteria</th>
                    <th style="width:80px">Jenis</th>
                    <th>Bobot (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($periodeKriteria as $i => $pk)
                @php $kode = 'C'.($i+1); @endphp
                <tr>
                    <td><span class="badge bg-gray-soft" style="font-size:10px">{{ $kode }}</span></td>
                    <td style="font-weight:600;color:#1e293b">{{ $pk->nama_kriteria }}</td>
                    <td>
                        <span class="badge {{ $pk->jenis=='benefit'?'bg-success-soft':'bg-danger-soft' }}">
                            {{ ucfirst($pk->jenis) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            {{-- Progress bar visual --}}
                            <div style="flex:1;height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;min-width:80px">
                                <div id="bar-{{ $pk->id }}" style="height:100%;background:#2563eb;border-radius:3px;width:{{ old('bobot.'.$pk->id, $pk->bobot) }}%;transition:width .2s"></div>
                            </div>
                            {{-- Input number --}}
                            <input type="number"
                                name="bobot[{{ $pk->id }}]"
                                id="inp-{{ $pk->id }}"
                                data-bar="bar-{{ $pk->id }}"
                                class="form-control bobot-inp"
                                value="{{ old('bobot.'.$pk->id, $pk->bobot) }}"
                                min="0" max="100" step="1" required
                                style="width:64px;text-align:center;padding:4px 6px">
                        </div>
                    </td>
                </tr>
                @endforeach
                {{-- Total row --}}
                <tr style="background:#f8fafc">
                    <td colspan="3" style="font-weight:600;font-size:12px;color:#374151">Total bobot</td>
                    <td>
                        <span id="total-display" style="font-weight:700;font-size:13px;color:{{ $total==100?'#27500A':'#ef4444' }}">
                            {{ $total }}% {{ $total!=100?'(kurang '.abs($kurang).'%)':'' }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    {{-- Tombol Aktifkan --}}
    <div style="padding:12px 14px;border-top:0.5px solid #e2e8f0">
        <div id="wrap-aktifkan">
        @if($total == 100)
            <button type="button" id="btn-aktifkan" class="btn btn-success"
                data-bs-toggle="modal" data-bs-target="#modalAktifkan"
                style="background:#22c55e;border-color:#22c55e;color:#fff">
                <i class="ti ti-player-play"></i> Aktifkan periode
            </button>
        @else
            <button id="btn-aktifkan" class="btn" disabled
                style="background:#f1f5f9;border:0.5px solid #e2e8f0;color:#94a3b8;cursor:not-allowed">
                <i class="ti ti-player-play"></i> Aktifkan periode (bobot belum 100%)
            </button>
        @endif
        </div>
    </div>
</div>

@endsection
{{-- Modal Konfirmasi Aktifkan --}}
<div class="modal fade" id="modalAktifkan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
        <div class="modal-content" style="border:0.5px solid #e2e8f0;border-radius:12px;overflow:hidden">
            <div style="background:#22c55e;padding:20px;text-align:center">
                <div style="width:52px;height:52px;background:rgba(255,255,255,.25);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                    <i class="ti ti-player-play" style="font-size:26px;color:#fff"></i>
                </div>
                <div style="color:#fff;font-size:15px;font-weight:700">Aktifkan Periode?</div>
            </div>
            <div style="padding:18px 20px">
                <p style="color:#374151;font-size:13px;margin:0 0 6px;text-align:center">
                    Periode <strong>{{ $labelBulan }}</strong> akan diaktifkan dengan bobot:
                </p>
                <div style="background:#f8fafc;border-radius:8px;padding:10px 12px;margin-bottom:14px">
                    @foreach($periodeKriteria as $i => $pk)
                    <div style="display:flex;justify-content:space-between;padding:3px 0;font-size:12px;{{ !$loop->last?'border-bottom:0.5px solid #e2e8f0':'' }}">
                        <span style="color:#374151">C{{ $i+1 }} — {{ $pk->nama_kriteria }}</span>
                        <span style="font-weight:600;color:#2563eb">{{ $pk->bobot }}%</span>
                    </div>
                    @endforeach
                </div>
                <div class="alert-spk al-warn" style="margin-bottom:14px;font-size:11px">
                    <i class="ti ti-alert-triangle"></i>
                    Setelah diaktifkan, bobot <strong>tidak dapat diubah</strong> lagi.
                </div>
                <div style="display:flex;gap:8px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="flex:1;justify-content:center">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('periode.aktifkan', $periode) }}" style="flex:1">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" style="background:#22c55e;border-color:#22c55e;color:#fff;justify-content:center">
                            <i class="ti ti-check"></i> Ya, Aktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const inputs   = document.querySelectorAll('.bobot-inp');
const totalEl  = document.getElementById('total-display');
const cardTotal = document.getElementById('card-total');
const lblTotal  = document.getElementById('lbl-total');
const valTotal  = document.getElementById('val-total');
const cardKurang= document.getElementById('card-kurang');
const lblKurang = document.getElementById('lbl-kurang');
const valKurang = document.getElementById('val-kurang');
const wrapBtn   = document.getElementById('wrap-aktifkan');

function recalc() {
    let sum = 0;
    inputs.forEach(inp => {
        const val = parseFloat(inp.value) || 0;
        sum += val;
        const bar = document.getElementById(inp.dataset.bar);
        if (bar) bar.style.width = Math.min(val, 100) + '%';
    });
    const selisih = Math.round((100 - sum) * 100) / 100;
    const ok      = sum === 100;
    const lebih   = sum > 100;
    const kurang  = sum < 100;

    // --- Update total row di tabel ---
    if (ok) {
        totalEl.style.color = '#27500A';
        totalEl.textContent = '100%';
    } else if (lebih) {
        totalEl.style.color = '#ef4444';
        totalEl.textContent = sum + '% (kelebihan ' + Math.abs(selisih) + '%)';
    } else {
        totalEl.style.color = '#ef4444';
        totalEl.textContent = sum + '% (kurang ' + Math.abs(selisih) + '%)';
    }

    // --- Update stat card Total ---
    const totalColor = ok ? {border:'#97C459',bg:'#EAF3DE',lbl:'#3B6D11',val:'#27500A'}
                     : lebih ? {border:'#fca5a5',bg:'#FCEBEB',lbl:'#791F1F',val:'#ef4444'}
                     : {border:'#f59e0b',bg:'#FAEEDA',lbl:'#854F0B',val:'#633806'};
    cardTotal.style.borderColor = totalColor.border;
    cardTotal.style.background  = totalColor.bg;
    lblTotal.style.color = totalColor.lbl;
    valTotal.style.color = totalColor.val;
    valTotal.textContent = sum + '%';

    // --- Update stat card Kekurangan (judul berubah jika lebih) ---
    const kurangColor = ok    ? {border:'#97C459',bg:'#EAF3DE',lbl:'#3B6D11',val:'#27500A'}
                      : lebih ? {border:'#fca5a5',bg:'#FCEBEB',lbl:'#791F1F',val:'#ef4444'}
                      :         {border:'#fca5a5',bg:'#FCEBEB',lbl:'#791F1F',val:'#ef4444'};
    cardKurang.style.borderColor = kurangColor.border;
    cardKurang.style.background  = kurangColor.bg;
    lblKurang.style.color = kurangColor.lbl;
    valKurang.style.color = kurangColor.val;
    lblKurang.textContent = lebih ? 'Kelebihan bobot' : 'Kekurangan bobot';
    valKurang.textContent = ok ? '0%' : Math.abs(selisih) + '%';

    // --- Update tombol Aktifkan ---
    if (ok) {
        wrapBtn.innerHTML = `<button type="button" id="btn-aktifkan" class="btn btn-success"
            data-bs-toggle="modal" data-bs-target="#modalAktifkan"
            style="background:#22c55e;border-color:#22c55e;color:#fff">
            <i class="ti ti-player-play"></i> Aktifkan periode
        </button>`;
        const newBtn = document.getElementById('btn-aktifkan');
        newBtn.addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('modalAktifkan')).show();
        });
    } else if (lebih) {
        wrapBtn.innerHTML = `<button class="btn" disabled
            style="background:#FCEBEB;border:0.5px solid #fca5a5;color:#ef4444;cursor:not-allowed">
            <i class="ti ti-alert-triangle"></i> Kelebihan ${Math.abs(selisih)}% — kurangi bobot
        </button>`;
    } else {
        wrapBtn.innerHTML = `<button class="btn" disabled
            style="background:#f1f5f9;border:0.5px solid #e2e8f0;color:#94a3b8;cursor:not-allowed">
            <i class="ti ti-player-play"></i> Aktifkan periode (bobot belum 100%)
        </button>`;
    }
}

inputs.forEach(inp => inp.addEventListener('input', recalc));
</script>
@endpush