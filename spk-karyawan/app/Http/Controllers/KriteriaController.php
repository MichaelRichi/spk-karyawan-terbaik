<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKriteriaRequest;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::withCount('subKriteria')->orderBy('id')->get();
        $kriteriaTetap      = $kriteria->where('tipe', 'tetap')->values();
        $kriteriaTidakTetap = $kriteria->where('tipe', 'tidak_tetap')->values();
        return view('kriteria.index', compact('kriteriaTetap', 'kriteriaTidakTetap'));
    }

    public function create(Request $request)
    {
        $tipe = $request->query('tipe') === 'tidak_tetap' ? 'tidak_tetap' : 'tetap';
        $totalBobot = Kriteria::where('tipe', $tipe)->sum('bobot');
        return view('kriteria.create', compact('totalBobot', 'tipe'));
    }

    public function edit(int $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        // Total bobot dalam set tipe yang sama, di luar kriteria ini
        $totalBobot = Kriteria::where('tipe', $kriteria->tipe)
            ->where('id', '!=', $id)->sum('bobot');
        return view('kriteria.edit', compact('kriteria', 'totalBobot'));
    }

    public function store(StoreKriteriaRequest $request)
    {
        $data = $request->validated();
        $data['has_rentang'] = $request->boolean('has_rentang');
        $data['satuan_rentang'] = $this->parseSatuanRentang($request, $data['has_rentang']);
        $kriteria = Kriteria::create($data);
        return redirect()->route('kriteria.sub-kriteria', $kriteria)
            ->with('success', "Kriteria {$kriteria->nama} ditambahkan. Silakan tambah skala penilaian.");
    }

    public function update(StoreKriteriaRequest $request, int $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $data = $request->validated();
        $data['has_rentang'] = $request->boolean('has_rentang');
        $data['satuan_rentang'] = $this->parseSatuanRentang($request, $data['has_rentang']);
        $kriteria->update($data);
        return redirect()->route('kriteria.index')
            ->with('success', "Kriteria {$kriteria->nama} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        // Blokir HANYA jika kriteria masih dipakai periode yang BELUM selesai
        // (status draft/aktif). Periode yang sudah selesai menyimpan snapshot
        // sendiri (periode_kriteria), sehingga menghapus kriteria master tidak
        // mengubah hasil ranking periode-periode sebelumnya — hanya tidak akan
        // dipakai lagi pada periode berikutnya.
        $dipakaiPeriodeBerjalan = $kriteria->periodeKriteria()
            ->whereHas('periode', fn ($q) => $q->where('status', '!=', 'selesai'))
            ->exists();

        if ($dipakaiPeriodeBerjalan) {
            return back()->with('error',
                "Kriteria \"{$kriteria->nama}\" tidak dapat dihapus karena masih digunakan pada periode penilaian yang sedang berjalan (belum selesai). "
                . "Selesaikan atau hapus periode tersebut terlebih dahulu."
            );
        }

        $nama = $kriteria->nama;
        $tipe = $kriteria->tipe;
        $tipeLabel = $tipe === 'tetap' ? 'tetap' : 'tidak tetap';
        $kriteria->delete(); // sub-kriteria ikut terhapus; snapshot periode lama tetap utuh

        $pesan = "Kriteria \"{$nama}\" beserta sub-kriterianya berhasil dihapus. Periode penilaian yang sudah selesai tidak terpengaruh; kriteria ini hanya tidak akan dipakai pada periode berikutnya.";

        // Setelah penghapusan, total bobot set tipe ini kemungkinan tidak lagi 100%.
        // Beri peringatan eksplisit agar pengguna menyeimbangkan ulang bobot sebelum
        // membuat periode penilaian (periode tetap diblokir bila bobot != 100%).
        if (Kriteria::where('tipe', $tipe)->count() === 0) {
            return redirect()->route('kriteria.index')
                ->with('warning', $pesan . " Belum ada kriteria tersisa untuk karyawan {$tipeLabel}. Tambahkan kriteria sebelum membuat periode penilaian.");
        }

        $totalBobot = Kriteria::where('tipe', $tipe)->sum('bobot');
        if (abs($totalBobot - 100) > 0.01) {
            return redirect()->route('kriteria.index')
                ->with('warning',
                    $pesan . " Total bobot kriteria karyawan {$tipeLabel} sekarang {$totalBobot}% (belum 100%). "
                    . "Sesuaikan kembali bobotnya agar periode penilaian dapat dibuat."
                );
        }

        return redirect()->route('kriteria.index')->with('success', $pesan);
    }

    // ── Sub-Kriteria via menu sidebar ─────────────────────────

    /**
     * Tampilkan semua sub-kriteria dari semua kriteria
     * Diakses dari menu sidebar → Sub-Kriteria
     */
    public function subKriteriaAll()
    {
        $tipe = request('tipe') === 'tidak_tetap' ? 'tidak_tetap' : 'tetap';
        $kriteria = Kriteria::with('subKriteria')->where('tipe', $tipe)->orderBy('id')->get();
        $jumlah = [
            'tetap'       => Kriteria::where('tipe', 'tetap')->count(),
            'tidak_tetap' => Kriteria::where('tipe', 'tidak_tetap')->count(),
        ];
        return view('sub_kriteria.all', compact('kriteria', 'tipe', 'jumlah'));
    }

    // ── Sub-Kriteria via halaman Kriteria ─────────────────────

    public function subKriteriaIndex(int $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->load('subKriteria');
        return view('sub_kriteria.index', compact('kriteria'));
    }

    public function subKriteriaCreate(int $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('sub_kriteria.create', compact('kriteria'));
    }

    public function subKriteriaEdit(int $kriteriaId, SubKriteria $subKriteria)
    {
        $kriteria = Kriteria::findOrFail($kriteriaId);
        return view('sub_kriteria.edit', compact('kriteria', 'subKriteria'));
    }

    public function subKriteriaStore(Request $request, int $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'skor'      => ['required', 'integer', 'min:1', 'max:10',
                'unique:sub_kriteria,skor,NULL,id,kriteria_id,' . $kriteria->id],
            'nilai_min' => ['nullable', 'numeric', 'min:0'],
            'nilai_max' => ['nullable', 'numeric', 'min:0'],
        ], ['skor.unique' => 'Skor ini sudah ada untuk kriteria tersebut.']);

        $kriteria->subKriteria()->create($data);
        return redirect()->route('kriteria.sub-kriteria', $kriteria)
            ->with('success', 'Skala penilaian berhasil ditambahkan.');
    }

    public function subKriteriaUpdate(Request $request, int $kriteriaId, SubKriteria $subKriteria)
    {
        $kriteria = Kriteria::findOrFail($kriteriaId);
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'skor'      => ['required', 'integer', 'min:1', 'max:10',
                'unique:sub_kriteria,skor,' . $subKriteria->id . ',id,kriteria_id,' . $kriteria->id],
            'nilai_min' => ['nullable', 'numeric', 'min:0'],
            'nilai_max' => ['nullable', 'numeric', 'min:0'],
        ], ['skor.unique' => 'Skor ini sudah ada untuk kriteria tersebut.']);

        $subKriteria->update($data);
        return redirect()->route('kriteria.sub-kriteria', $kriteria)
            ->with('success', 'Skala penilaian berhasil diperbarui.');
    }

    private function parseSatuanRentang(Request $request, bool $hasRentang): ?string
    {
        if (!$hasRentang) return null;
        return strtolower(trim($request->input('satuan_rentang', ''))) ?: null;
    }

    public function subKriteriaDestroy(int $kriteriaId, SubKriteria $subKriteria)
    {
        $kriteria = Kriteria::findOrFail($kriteriaId);
        if ($subKriteria->periodeSubKriteria()->exists()) {
            return back()->with('error',
                'Skala tidak dapat dihapus karena sudah digunakan di periode penilaian.'
            );
        }
        $subKriteria->delete();
        return back()->with('success', 'Skala penilaian berhasil dihapus.');
    }
}