<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenilaianRequest;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\PeriodeSubKriteria;
use App\Models\HasilRanking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /** Daftar karyawan beserta status penilaiannya */
    public function index(Periode $periode)
    {
        if ($periode->status === 'draft') {
            return redirect()->route('periode.show', $periode)
                ->with('error', 'Aktifkan periode terlebih dahulu sebelum input penilaian.');
        }

        $karyawan        = Karyawan::aktif()->orderBy('nama')->get();
        $periodeKriteria = $periode->periodeKriteria()->get();
        $jumlahKriteria  = $periodeKriteria->count();

        $semuaPenilaian = Penilaian::where('periode_id', $periode->id)
            ->with('periodeSubKriteria')
            ->get();

        // [karyawan_id][periode_kriteria_id] => Penilaian
        $nilaiKaryawan = [];
        foreach ($semuaPenilaian as $p) {
            $nilaiKaryawan[$p->karyawan_id][$p->periode_kriteria_id] = $p;
        }

        // Collection of karyawan IDs where every criterion has been assessed
        $penilaianSelesai = $semuaPenilaian
            ->groupBy('karyawan_id')
            ->filter(fn($group) => $group->count() >= $jumlahKriteria)
            ->keys();

        return view('penilaian.index', compact(
            'periode', 'karyawan', 'nilaiKaryawan', 'penilaianSelesai'
        ));
    }

    /** Form input penilaian satu karyawan */
    public function form(Periode $periode, Karyawan $karyawan)
    {
        if ($periode->isLocked()) {
            return redirect()->route('penilaian.index', $periode)
                ->with('error', 'Periode sudah selesai, penilaian tidak dapat diubah.');
        }

        $periodeKriteria = $periode->periodeKriteria()
            ->with('periodeSubKriteria')
            ->get();

        // Ambil pilihan yang sudah tersimpan (untuk mode edit)
        $nilaiExisting = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->pluck('periode_sub_kriteria_id', 'periode_kriteria_id');

        return view('penilaian.form', compact(
            'periode', 'karyawan', 'periodeKriteria', 'nilaiExisting'
        ));
    }

    /** Simpan / update penilaian satu karyawan */
    public function simpan(StorePenilaianRequest $request, Periode $periode, Karyawan $karyawan)
    {
        if ($periode->isLocked()) {
            return back()->with('error', 'Periode sudah selesai.');
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
                        'nilai_normalisasi'        => null, // reset, dihitung ulang saat SAW
                        'nilai_terbobot'           => null,
                        'catatan'                  => $request->input("catatan.{$pkId}"),
                        'dinilai_oleh'             => Auth::id(),
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

    /** Detail penilaian satu karyawan (bisa dilihat karyawan sendiri) */
    public function detail(Periode $periode, Karyawan $karyawan)
    {
        // Karyawan hanya boleh lihat penilaian dirinya sendiri
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($authUser->isKaryawan() && $authUser->karyawan_id !== $karyawan->id) {
            abort(403, 'Anda hanya dapat melihat penilaian Anda sendiri.');
        }

        $penilaian = Penilaian::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->with(['periodeKriteria', 'periodeSubKriteria'])
            ->get();

        $ranking = HasilRanking::where('periode_id', $periode->id)
            ->where('karyawan_id', $karyawan->id)
            ->first();

        return view('penilaian.detail', compact('periode', 'karyawan', 'penilaian', 'ranking'));
    }
}