<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Hasil Ranking — {{ $periode->bulan }}/{{ $periode->tahun }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:12px;margin:20px}
h2,h3{text-align:center;margin:4px 0}
table{width:100%;border-collapse:collapse;margin-top:12px}
th,td{border:1px solid #333;padding:5px 8px;text-align:center}
th{background:#f0f0f0;font-weight:bold}
td.left{text-align:left}
.kop{text-align:center;margin-bottom:12px;border-bottom:2px solid #333;padding-bottom:8px}
</style>
</head>
<body>
<div class="kop">
    <h2>PT CEMPAKA INDAH ABADI</h2>
    <h3>HASIL RANKING KARYAWAN TERBAIK</h3>
    <div>Periode: {{ $periode->bulan }}/{{ $periode->tahun }}</div>
</div>
<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Nama Karyawan</th>
            @foreach($periode->periodeKriteria as $pk)
            <th>{{ $pk->nama_kriteria }} ({{ $pk->bobot }}%)</th>
            @endforeach
            <th>Nilai Vi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $d)
        <tr>
            <td>{{ $d['ranking'] }}</td>
            <td class="left">{{ $d['karyawan']->nama }}</td>
            @foreach($d['detail_kriteria'] as $dk)
            <td>{{ $dk['nilai'] }} / {{ number_format($dk['nilai_normalisasi'],3) }}</td>
            @endforeach
            <td><strong>{{ number_format($d['nilai_preferensi'],4) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div style="margin-top:30px;text-align:right">
    Palembang, {{ date('d/m/Y') }}<br><br><br>
    <u>Herman Daniel, BE</u><br>
    Direktur
</div>
</body>
</html>