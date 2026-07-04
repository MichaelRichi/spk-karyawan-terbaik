<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodeRequest;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Services\PeriodeService;

class PeriodeController extends Controller
{
    public function __construct(private PeriodeService $periodeService) {}

    public function index()
    {
        $query = Periode::with(['penilaian','hasilRanking.karyawan','periodeKriteria'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan');

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('tahun', 'like', "%{$search}%")
                  ->orWhereRaw("LOWER(nama) LIKE ?", ['%'.strtolower($search).'%']);
            });
        }

        if ($tahun = request('tahun')) {
            $query->where('tahun', $tahun);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $periode   = $query->paginate(12)->withQueryString();
        $tahunList = Periode::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('periode.index', compact('periode', 'tahunList'));
    }

    public function create()
    {
        // Periode mencakup kedua tipe; tiap set kriteria harus 100%.
        $setKriteria = [];
        $bisaBuat    = true;
        foreach (Periode::tipeList() as $tipe) {
            $kriteria   = Kriteria::with('subKriteria')->where('tipe', $tipe)->get();
            $totalBobot = $kriteria->sum('bobot');
            $valid      = $kriteria->isNotEmpty() && $totalBobot == 100;
            $setKriteria[$tipe] = [
                'kriteria'   => $kriteria,
                'totalBobot' => $totalBobot,
                'valid'      => $valid,
            ];
            if (!$valid) { $bisaBuat = false; }
        }

        return view('periode.create', compact('setKriteria', 'bisaBuat'));
    }

    /**
     * Buat periode baru (per bulan) → snapshot KEDUA set kriteria → status aktif.
     * Pemisahan tetap/tidak tetap terjadi di dalam penilaian.
     */
    public function store(StorePeriodeRequest $request)
    {
        // Validasi server: tiap set kriteria harus 100% dan tidak kosong
        foreach (Periode::tipeList() as $tipe) {
            $label      = Periode::tipeLabel($tipe);
            $kriteria   = Kriteria::where('tipe', $tipe)->get();
            $totalBobot = $kriteria->sum('bobot');
            if ($kriteria->isEmpty()) {
                return back()->with('error', "Belum ada kriteria untuk {$label}. Tambahkan di menu Kriteria terlebih dahulu.")->withInput();
            }
            if ($totalBobot != 100) {
                return back()->with('error', "Total bobot kriteria {$label} harus 100%. Saat ini {$totalBobot}%. Atur di menu Kriteria terlebih dahulu.")->withInput();
            }
        }

        $namaBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        $data = array_merge($request->validated(), [
            'nama'   => $namaBulan[$request->bulan] . ' ' . $request->tahun,
            'status' => 'aktif', // langsung aktif
        ]);

        $periode = $this->periodeService->buat($data);

        return redirect()->route('penilaian.index', $periode)
            ->with('success', "Periode {$periode->nama} berhasil dibuat. Silakan input penilaian.");
    }

    public function show(Periode $periode)
    {
        $periodeKriteria = $periode->periodeKriteria()->with('kriteria')->get();
        $totalBobot      = $periodeKriteria->sum('bobot');
        return view('periode.show', compact('periode', 'periodeKriteria', 'totalBobot'));
    }

    /** Selesaikan & kunci periode (aktif → selesai) */
    public function selesaikan(Periode $periode)
    {
        try {
            $this->periodeService->selesaikan($periode);
            return redirect()->route('periode.show', $periode)
                ->with('success', "Periode {$periode->nama} berhasil dikunci. Data tidak dapat diubah lagi.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /** Buka kembali periode yang terkunci agar nilai dapat dikoreksi */
    public function buka(Periode $periode)
    {
        if ($periode->status !== 'selesai') {
            return back()->with('error', 'Periode ini tidak dalam keadaan terkunci.');
        }
        $periode->update(['status' => 'aktif']);
        return redirect()->route('ranking.hasil', $periode)
            ->with('warning', "Periode {$periode->nama} dibuka kembali. Silakan koreksi nilai, lalu jalankan Hitung Penilaian untuk memperbarui ranking dan mengunci kembali.");
    }

    /** Hapus periode aktif beserta semua data terkait */
    public function hapus(Periode $periode)
    {
        if ($periode->status !== 'aktif') {
            return back()->with('error', 'Hanya periode aktif yang dapat dihapus.');
        }

        $nama = $periode->nama;
        $periode->delete(); // cascade: periode_kriteria, penilaian, hasil_ranking ikut terhapus

        return redirect()->route('periode.index')
            ->with('success', 'Hapus Periode');
    }
}