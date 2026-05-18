<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Ranking — {{ ((['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$periode->bulan]) ?? $periode->bulan).' '.$periode->tahun }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    padding: 40px 50px;
}

/* ── KOP SURAT ── */
.kop {
    text-align: center;
    border-bottom: 3px double #000;
    padding-bottom: 12px;
    margin-bottom: 20px;
}
.kop-nama {
    font-size: 16pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.kop-judul {
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
    margin: 4px 0 2px;
}
.kop-sub {
    font-size: 10pt;
    color: #333;
}

/* ── INFO DOKUMEN ── */
.info-grid {
    display: grid;
    grid-template-columns: 140px 8px 1fr;
    gap: 3px 0;
    font-size: 10pt;
    margin-bottom: 18px;
    padding: 10px 14px;
    background: #f8f8f8;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.info-grid span { line-height: 1.6; }

/* ── TABEL ── */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
    margin-bottom: 30px;
}
thead tr {
    background: #1a1a2e;
    color: #fff;
}
thead th {
    padding: 8px 6px;
    text-align: center;
    font-weight: bold;
    font-size: 9.5pt;
    border: 1px solid #1a1a2e;
    line-height: 1.4;
}
tbody td {
    padding: 6px 8px;
    border: 1px solid #ccc;
    text-align: center;
    vertical-align: middle;
}
tbody td.left { text-align: left; }
tbody tr:nth-child(even) { background: #f5f5f5; }
tbody tr.winner {
    background: #fef9e7;
    font-weight: bold;
}
tbody tr.winner td {
    border-color: #c9a227;
}
.rank-1 {
    background: #1a1a2e;
    color: #fff;
    font-weight: bold;
    border-radius: 50%;
    display: inline-block;
    width: 22px;
    height: 22px;
    line-height: 22px;
    text-align: center;
    font-size: 9pt;
}
.rank-num {
    font-weight: 600;
    color: #444;
}
.nilai-akhir {
    font-weight: bold;
    font-size: 11pt;
    color: #1a1a2e;
}
.nilai-norm {
    font-size: 8.5pt;
    color: #555;
}

/* ── KETERANGAN ── */
.keterangan {
    font-size: 9pt;
    color: #444;
    border-top: 1px solid #ddd;
    padding-top: 8px;
    margin-bottom: 30px;
}

/* ── TTD ── */
.ttd-section {
    display: flex;
    justify-content: flex-end;
}
.ttd-box {
    text-align: center;
    font-size: 10.5pt;
}
.ttd-kota { margin-bottom: 50px; }
.ttd-nama {
    font-weight: bold;
    border-bottom: 1.5px solid #000;
    padding-bottom: 1px;
    display: inline-block;
}
.ttd-jabatan { font-size: 9.5pt; color: #333; margin-top: 3px; }

/* ── FOOTER ── */
.footer {
    position: fixed;
    bottom: 20px;
    left: 50px;
    right: 50px;
    border-top: 1px solid #ccc;
    padding-top: 5px;
    display: flex;
    justify-content: space-between;
    font-size: 8.5pt;
    color: #777;
}
</style>
</head>
<body>

{{-- KOP SURAT --}}
<div class="kop">
    <div class="kop-nama">PT Cempaka Indah Abadi</div>
    <div class="kop-judul">Laporan Hasil Ranking Karyawan Terbaik</div>
    <div class="kop-sub">Metode Simple Additive Weighting (SAW)</div>
</div>

{{-- INFO DOKUMEN --}}
@php
    $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $labelBulan = ($namaBulan[$periode->bulan] ?? $periode->bulan).' '.$periode->tahun;
    $totalKriteria = $periode->periodeKriteria->count();
    $totalKaryawan = count($detail);
@endphp
<div class="info-grid">
    <span>Periode</span><span>:</span><span>{{ $labelBulan }}</span>
    <span>Total Karyawan</span><span>:</span><span>{{ $totalKaryawan }} karyawan</span>
    <span>Total Kriteria</span><span>:</span><span>{{ $totalKriteria }} kriteria</span>
    <span>Tanggal Cetak</span><span>:</span><span>{{ date('d F Y') }}</span>
</div>

{{-- TABEL RANKING --}}
<table>
    <thead>
        <tr>
            <th style="width:45px">Rank</th>
            <th style="text-align:left">Nama Karyawan</th>
            @foreach($periode->periodeKriteria as $pk)
            <th>{{ $pk->nama_kriteria }}<br><span style="font-weight:normal;font-size:8.5pt">({{ $pk->bobot }}%)</span></th>
            @endforeach
            <th style="width:80px">Nilai Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $d)
        <tr class="{{ $d['ranking']==1 ? 'winner' : '' }}">
            <td>
                @if($d['ranking'] == 1)
                    <span class="rank-1">1</span>
                @else
                    <span class="rank-num">{{ $d['ranking'] }}</span>
                @endif
            </td>
            <td class="left">
                {{ $d['karyawan']->nama }}
                @if($d['ranking'] == 1)
                <span style="font-size:8pt;color:#8a7000;margin-left:4px">&#9733; Terbaik</span>
                @endif
            </td>
            @foreach($d['detail_kriteria'] as $dk)
            <td>
                <span style="font-weight:600">{{ $dk['nilai'] }}</span>
                <br>
                <span class="nilai-norm">{{ number_format($dk['nilai_normalisasi'],3) }}</span>
            </td>
            @endforeach
            <td class="nilai-akhir">{{ number_format($d['nilai_preferensi'],4) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- KETERANGAN --}}
<div class="keterangan">
    <strong>Keterangan:</strong>
    Angka atas = skor penilaian &nbsp;|&nbsp;
    Angka bawah = nilai normalisasi &nbsp;|&nbsp;
    Nilai Akhir = &Sigma;(Bobot &times; Normalisasi) &nbsp;|&nbsp;
    Benefit: r = x / Max(x) &nbsp;|&nbsp;
    Cost: r = Min(x) / x
</div>

{{-- TANDA TANGAN --}}
<div class="ttd-section">
    <div class="ttd-box">
        <div class="ttd-kota">Palembang, {{ date('d F Y') }}</div>
        <div class="ttd-nama">Herman Daniel, BE</div>
        <div class="ttd-jabatan">Direktur PT Cempaka Indah Abadi</div>
    </div>
</div>

{{-- FOOTER --}}
<div class="footer">
    <span>PT Cempaka Indah Abadi — Laporan Rahasia</span>
    <span>Dicetak: {{ date('d/m/Y H:i') }}</span>
</div>

</body>
</html>