<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodeRequest;
use App\Http\Requests\UpdateBobotRequest;
use App\Models\Periode;
use App\Models\PeriodeKriteria;
use App\Services\PeriodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodeController extends Controller
{
    public function __construct(private PeriodeService $periodeService) {}

    /** Daftar semua periode */
    public function index()
    {
        $periode = Periode::withCount('penilaian')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->paginate(12);

        return view('periode.index', compact('periode'));
    }

    public function create()
    {
        return view('periode.create');
    }

    /**
     * Buat periode baru.
     * Otomatis snapshot kriteria aktif ke periode_kriteria.
     */
    public function store(StorePeriodeRequest $request)
    {
        $namaBulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        $data = array_merge($request->validated(), [
            'nama'        => $namaBulan[$request->bulan] . ' ' . $request->tahun,
            'dibuat_oleh' => Auth::user()->id,
        ]);

        $periode = $this->periodeService->buat($data);

        return redirect()->route('periode.bobot', $periode)
            ->with('success', "Periode {$periode->nama} berhasil dibuat. Sesuaikan bobot kriteria di bawah ini.");
    }

    /** Halaman detail periode */
    public function show(Periode $periode)
    {
        $periodeKriteria = $periode->periodeKriteria()->with('kriteria')->get();
        $totalBobot      = $periodeKriteria->sum('bobot');
        return view('periode.show', compact('periode', 'periodeKriteria', 'totalBobot'));
    }

    /** Form atur bobot khusus periode ini */
    public function bobot(Periode $periode)
    {
        if ($periode->isLocked()) {
            return redirect()->route('periode.show', $periode)
                ->with('error', 'Periode sudah selesai, bobot tidak dapat diubah.');
        }

        $periodeKriteria = $periode->periodeKriteria()->with('kriteria')->get();
        $totalBobot      = $periodeKriteria->sum('bobot');

        return view('periode.bobot', compact('periode', 'periodeKriteria', 'totalBobot'));
    }

    /**
     * Simpan bobot kriteria untuk periode ini.
     * PENTING: Hanya memengaruhi periode ini, bukan periode lain.
     */
    public function updateBobot(UpdateBobotRequest $request, Periode $periode)
    {
        if ($periode->isLocked()) {
            return back()->with('error', 'Periode sudah selesai, bobot tidak dapat diubah.');
        }

        foreach ($request->input('bobot') as $pkId => $bobot) {
            PeriodeKriteria::where('id', $pkId)
                ->where('periode_id', $periode->id) // keamanan: hanya update milik periode ini
                ->update(['bobot' => $bobot]);
        }

        return redirect()->route('periode.show', $periode)
            ->with('success', 'Bobot kriteria berhasil disimpan untuk periode ini.');
    }

    /** Aktifkan periode (draft → aktif) */
    public function aktifkan(Periode $periode)
    {
        if ($periode->status !== 'draft') {
            return back()->with('error', 'Hanya periode berstatus draft yang dapat diaktifkan.');
        }

        if (!$periode->isBobotValid()) {
            $total = $periode->periodeKriteria()->sum('bobot');
            return back()->with('error', "Total bobot harus 100%. Saat ini: {$total}%.");
        }

        $periode->update(['status' => 'aktif']);

        return back()->with('success', "Periode {$periode->nama} berhasil diaktifkan. Input penilaian dapat dimulai.");
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