<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenilaianRequest;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Absensi;
use App\Models\PeriodeSubKriteria;
use App\Models\HasilRanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index(Periode $periode)
    {
        if ($periode->status === 'draft') {
            return redirect()->route('periode.show', $periode)
                ->with('error', 'Aktifkan periode terlebih dahulu sebelum input penilaian.');
        }

        if ($periode->status === 'selesai') {
            return redirect()->route('ranking.hasil', $periode)
                ->with('info', 'Periode ini sudah selesai dan terkunci. Berikut adalah hasil rankingnya.');
        }

        // Auto-isi nilai Kehadiran & Kedisiplinan dari data absensi yang sudah
        // diimport (untuk karyawan yang belum punya nilai di periode ini).
        $terisiOtomatis = $this->isiNilaiDariAbsensi($periode);
        if ($terisiOtomatis > 0) {
            return redirect()->route('penilaian.index', $periode)
                ->with('success', "Nilai Kehadiran & Kedisiplinan {$terisiOtomatis} karyawan otomatis diisi dari data absensi {$periode->nama_bulan_lengkap}.");
        }

        // Hanya karyawan AKTIF yang bisa dinilai
        $karyawan = Karyawan::aktif()->orderBy('nama')->get();

        $jumlahKriteria = $periode->periodeKriteria()->count();

        // ID karyawan yang sudah dinilai lengkap (semua kriteria)
        $penilaianSelesai = Penilaian::where('periode_id', $periode->id)
            ->select('karyawan_id', DB::raw('count(*) as jumlah'))
            ->groupBy('karyawan_id')
            ->having('jumlah', '>=', $jumlahKriteria)
            ->pluck('jumlah', 'karyawan_id')
            ->keys();

        // Nilai yang sudah diinput per karyawan per kriteria
        $nilaiKaryawan = [];
        $penilaianAll = Penilaian::where('periode_id', $periode->id)
            ->with('periodeSubKriteria')
            ->get();
        foreach ($penilaianAll as $p) {
            $nilaiKaryawan[$p->karyawan_id][$p->periode_kriteria_id] = $p;
        }

        return view('penilaian.index', compact(
            'periode', 'karyawan', 'penilaianSelesai', 'jumlahKriteria', 'nilaiKaryawan'
        ));
    }

    public function form(Periode $periode, Karyawan $karyawan)
    {
        if ($periode->status === 'selesai') {
            return redirect()->route('ranking.hasil', $periode)
                ->with('info', 'Periode ini sudah selesai dan terkunci.');
        }

        // Cek karyawan aktif
        if (!$karyawan->isAktif()) {
            return redirect()->route('penilaian.index', $periode)
                ->with('error', "Karyawan {$karyawan->nama} tidak aktif dan tidak dapat dinilai.");
        }

        $periodeKriteria = $periode->periodeKriteria()
            ->with('periodeSubKriteria')
            ->get();

        // Nilai yang sudah tersimpan (untuk mode edit)
        $nilaiExisting = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->get()
            ->keyBy('periode_kriteria_id');

        return view('penilaian.form', compact(
            'periode', 'karyawan', 'periodeKriteria', 'nilaiExisting'
        ));
    }

    public function simpan(StorePenilaianRequest $request, Periode $periode, Karyawan $karyawan)
    {
        if ($periode->status === 'selesai') {
            return back()->with('error', 'Periode ini sudah selesai dan terkunci. Nilai tidak dapat diubah.');
        }

        if (!$karyawan->isAktif()) {
            return back()->with('error', "Karyawan {$karyawan->nama} tidak aktif.");
        }

        DB::beginTransaction();
        try {
            foreach ($request->input('penilaian') as $pkId => $pskId) {
                $psk = PeriodeSubKriteria::findOrFail($pskId);

                Penilaian::updateOrCreate(
                    [
                        'periode_id'          => $periode->id,
                        'karyawan_id'         => $karyawan->id,
                        'periode_kriteria_id' => (int) $pkId,
                    ],
                    [
                        'periode_sub_kriteria_id' => $psk->id,
                        'nilai'                   => $psk->skor,
                        'nilai_normalisasi'        => 0,
                        'nilai_terbobot'           => 0,
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }

        return redirect()->route('penilaian.index', $periode)
            ->with('success', "Penilaian {$karyawan->nama} berhasil disimpan.");
    }

    /**
     * Isi otomatis nilai Kehadiran (dari total hadir) & Kedisiplinan (dari total
     * terlambat) berdasarkan data absensi bulan & tahun periode ini.
     *
     * - Hanya mengisi nilai yang BELUM ada (tidak menimpa input/edit manual).
     * - Hanya untuk karyawan yang punya data absensi di bulan tsb.
     * - Mengembalikan jumlah karyawan yang mendapat minimal satu nilai baru.
     */
    private function isiNilaiDariAbsensi(Periode $periode): int
    {
        $pkKehadiran    = $periode->kriteriaKehadiran();
        $pkKedisiplinan = $periode->kriteriaKedisiplinan();

        if (!$pkKehadiran && !$pkKedisiplinan) {
            return 0; // periode tidak punya kriteria yang bisa diisi dari absensi
        }

        $terisi = 0;

        foreach (Karyawan::aktif()->get() as $karyawan) {
            // Karyawan harus punya data absensi di bulan & tahun periode
            $adaAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $periode->bulan)
                ->whereYear('tanggal', $periode->tahun)
                ->exists();
            if (!$adaAbsensi) continue;

            $totalHadir     = Absensi::totalHadir($karyawan->id, $periode->bulan, $periode->tahun);
            $totalTerlambat = Absensi::totalTerlambat($karyawan->id, $periode->bulan, $periode->tahun);

            $adaIsi  = $this->isiNilaiBilaBelum($periode, $pkKehadiran, $karyawan->id, $totalHadir);
            $adaIsi2 = $this->isiNilaiBilaBelum($periode, $pkKedisiplinan, $karyawan->id, $totalTerlambat);

            if ($adaIsi || $adaIsi2) $terisi++;
        }

        return $terisi;
    }

    /**
     * Buat nilai penilaian dari sebuah angka HANYA jika belum ada.
     * Tidak menimpa nilai yang sudah diinput. Return true bila membuat nilai baru.
     */
    private function isiNilaiBilaBelum(Periode $periode, $pk, int $karyawanId, int $nilai): bool
    {
        if (!$pk) return false;

        $sudahAda = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawanId)
            ->where('periode_kriteria_id', $pk->id)
            ->exists();
        if ($sudahAda) return false;

        $skor = Absensi::hitungSkor($nilai, $pk->id);
        $psk  = $pk->periodeSubKriteria->where('skor', $skor)->first();
        if (!$psk) return false;

        Penilaian::create([
            'periode_id'              => $periode->id,
            'karyawan_id'             => $karyawanId,
            'periode_kriteria_id'     => $pk->id,
            'periode_sub_kriteria_id' => $psk->id,
            'nilai'                   => $psk->skor,
        ]);
        return true;
    }
}