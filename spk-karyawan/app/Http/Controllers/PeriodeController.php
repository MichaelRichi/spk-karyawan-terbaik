<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodeRequest;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Services\PeriodeService;
use Illuminate\Support\Facades\Auth;

class PeriodeController extends Controller
{
    public function __construct(private PeriodeService $periodeService) {}

    public function index()
    {
        $query = Periode::with(['penilaian','hasilRanking.karyawan','periodeKriteria'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan');

        if ($search = (string) request('search')) {
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
        // Cek kriteria sudah ada dan total bobot = 100
        $kriteria    = Kriteria::with('subKriteria')->get();
        $totalBobot  = $kriteria->sum('bobot_default');
        $bisaBuat    = $kriteria->isNotEmpty() && $totalBobot == 100;

        return view('periode.create', compact('kriteria', 'totalBobot', 'bisaBuat'));
    }

    /**
     * Buat periode baru → snapshot kriteria → langsung status aktif
     */
    public function store(StorePeriodeRequest $request)
    {
        // Validasi server: bobot harus 100% sebelum buat periode
        $totalBobot = Kriteria::sum('bobot_default');
        if ($totalBobot != 100) {
            return back()->with('error', "Total bobot kriteria harus 100%. Saat ini {$totalBobot}%. Atur di menu Kriteria terlebih dahulu.");
        }

        $kriteria = Kriteria::with('subKriteria')->get();
        if ($kriteria->isEmpty()) {
            return back()->with('error', 'Belum ada kriteria. Tambahkan kriteria di menu Kriteria terlebih dahulu.');
        }

        $namaBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        $data = array_merge($request->validated(), [
            'nama'        => $namaBulan[$request->bulan] . ' ' . $request->tahun,
            'dibuat_oleh' => Auth::user()->id,
            'status'      => 'aktif', // langsung aktif
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
}