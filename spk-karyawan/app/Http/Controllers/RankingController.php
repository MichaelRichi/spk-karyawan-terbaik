<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\PeriodeSubKriteria;
use App\Services\SawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    public function __construct(private SawService $sawService) {}

    /** Halaman index ranking — pilih periode */
    public function index()
    {
        $periode = Periode::has('hasilRanking')
            ->with('hasilRanking.karyawan')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('ranking.index', compact('periode'));
    }

    /** Normalisasi tipe dari request */
    private function tipeDari(Request $request): string
    {
        return $request->input('tipe') === 'tidak_tetap' ? 'tidak_tetap' : 'tetap';
    }

    /** Jalankan perhitungan SAW untuk satu tipe */
    public function hitung(Request $request, Periode $periode)
    {
        $tipe  = $this->tipeDari($request);
        $label = Periode::tipeLabel($tipe);
        try {
            $this->sawService->hitung($periode, $tipe);
            return redirect()->route('ranking.hasil', $periode)
                ->with('success', "Perhitungan SAW untuk {$label} berhasil! Hasil ranking sudah diperbarui.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Tampilkan hasil ranking (kedua tipe) */
    public function hasil(Periode $periode)
    {
        if (!$periode->hasilRanking()->exists()) {
            return redirect()->route('penilaian.index', $periode)
                ->with('error', 'Belum ada hasil ranking. Jalankan hitung penilaian terlebih dahulu.');
        }

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $myId     = $authUser->isKaryawan() ? $authUser->karyawan_id : null;

        $detailPerTipe   = [];
        $kriteriaPerTipe = [];
        foreach (Periode::tipeList() as $tipe) {
            if (!$periode->hasilRanking()->where('tipe', $tipe)->exists()) {
                continue;
            }
            $detail = $this->sawService->getDetailRanking($periode, $tipe);
            if ($myId) {
                $detail = array_values(array_filter($detail, fn($d) => $d['karyawan']->id === $myId));
                if (empty($detail)) { continue; } // karyawan tipe lain
            }
            $detailPerTipe[$tipe]   = $detail;
            $kriteriaPerTipe[$tipe] = $periode->periodeKriteria()->where('tipe', $tipe)->get();
        }

        return view('ranking.hasil', compact('periode', 'detailPerTipe', 'kriteriaPerTipe'));
    }

    /** Form edit nilai karyawan di periode selesai */
    public function editNilai(Periode $periode, Karyawan $karyawan)
    {
        // Periode terkunci tidak boleh diedit — buka kunci terlebih dahulu
        if ($periode->isLocked()) {
            return redirect()->route('ranking.hasil', $periode)
                ->with('error', 'Periode terkunci. Tekan "Buka Kunci" terlebih dahulu untuk mengoreksi nilai.');
        }

        // Hanya kriteria sesuai tipe karyawan
        $periodeKriteria = $periode->periodeKriteria()->where('tipe', $karyawan->tipe)
            ->with('periodeSubKriteria')->get();
        $nilaiExisting   = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->get()->keyBy('periode_kriteria_id');

        return view('ranking.edit-nilai', compact('periode', 'karyawan', 'periodeKriteria', 'nilaiExisting'));
    }

    /** Simpan perubahan nilai dan hitung ulang SAW (tipe karyawan ybs) */
    public function updateNilai(Request $request, Periode $periode, Karyawan $karyawan)
    {
        if ($periode->isLocked()) {
            return redirect()->route('ranking.hasil', $periode)
                ->with('error', 'Periode terkunci. Tekan "Buka Kunci" terlebih dahulu untuk mengoreksi nilai.');
        }

        $request->validate(['penilaian' => 'required|array']);

        foreach ($request->penilaian as $pkId => $pskId) {
            $psk = PeriodeSubKriteria::findOrFail($pskId);
            Penilaian::updateOrCreate(
                ['periode_id' => $periode->id, 'karyawan_id' => $karyawan->id, 'periode_kriteria_id' => $pkId],
                ['periode_sub_kriteria_id' => $psk->id, 'nilai' => $psk->skor]
            );
        }

        // Ranking TIDAK dihitung ulang otomatis. Pengguna menjalankan
        // "Hitung Penilaian" secara manual setelah selesai mengoreksi.
        return redirect()->route('ranking.hasil', $periode)
            ->with('warning', "Nilai {$karyawan->nama} disimpan. Ranking belum diperbarui — buka menu Penilaian dan jalankan Hitung Penilaian untuk memperbarui ranking serta mengunci kembali periode.");
    }

    /** Cetak laporan PDF (kedua tipe) */
    public function cetak(Periode $periode)
    {
        if (!$periode->hasilRanking()->exists()) {
            return redirect()->route('ranking.index')
                ->with('error', 'Laporan hanya tersedia untuk periode yang sudah memiliki hasil.');
        }

        $detailPerTipe   = [];
        $kriteriaPerTipe = [];
        foreach (Periode::tipeList() as $tipe) {
            if (!$periode->hasilRanking()->where('tipe', $tipe)->exists()) {
                continue;
            }
            $detailPerTipe[$tipe]   = $this->sawService->getDetailRanking($periode, $tipe);
            $kriteriaPerTipe[$tipe] = $periode->periodeKriteria()->where('tipe', $tipe)->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'ranking.cetak',
            compact('periode', 'detailPerTipe', 'kriteriaPerTipe')
        )->setPaper('a4', 'landscape');

        return $pdf->download("Ranking_Karyawan_{$periode->nama}.pdf");
    }
}