<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Ranking — {{ $periode->bulan }}/{{ $periode->tahun }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:11px;margin:24px;color:#000}
h2,h3{text-align:center;margin:3px 0}
.kop{text-align:center;margin-bottom:14px;border-bottom:2px solid #000;padding-bottom:10px}
table{width:100%;border-collapse:collapse;margin-top:12px}
th,td{border:1px solid #999;padding:5px 8px;text-align:center}
th{background:#f0f0f0;font-weight:bold;font-size:10px}
td.left{text-align:left}
.winner{background:#fffbeb;font-weight:bold}
.ttd{margin-top:40px;text-align:right}
</style>
</head>
<body>
<div class="kop">
    <h2>PT CEMPAKA INDAH ABADI</h2>
    <h3>HASIL RANKING KARYAWAN TERBAIK</h3>
    <div>Metode Simple Additive Weighting (SAW)</div>
    <div>Periode: {{ $periode->bulan }}/{{ $periode->tahun }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Nama Karyawan</th>
            @foreach($periode->periodeKriteria as $pk)
            <th>{{ $pk->nama_kriteria }}<br>({{ $pk->bobot }}%)</th>
            @endforeach
            <th>Nilai Vi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $d)
        <tr class="{{ $d['ranking']==1?'winner':'' }}">
            <td>{{ $d['ranking'] == 1 ? '🥇 1' : $d['ranking'] }}</td>
            <td class="left">{{ $d['karyawan']->nama }}</td>
            @foreach($d['detail_kriteria'] as $dk)
            <td>{{ $dk['nilai'] }} / {{ number_format($dk['nilai_normalisasi'],3) }}</td>
            @endforeach
            <td><strong>{{ number_format($d['nilai_preferensi'],4) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="ttd">
    Palembang, {{ date('d/m/Y') }}<br><br><br>
    <u>Herman Daniel, BE</u><br>
    Direktur PT Cempaka Indah Abadi
</div>
</body>
</html>