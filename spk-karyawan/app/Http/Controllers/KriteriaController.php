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
        $kriteria = Kriteria::withCount('subKriteria')->orderBy('nama')->get();
        return view('kriteria.index', compact('kriteria'));
    }

    public function store(StoreKriteriaRequest $request)
    {
        $data = $request->validated();
        $data['has_rentang'] = $request->boolean('has_rentang');
        $kriteria = Kriteria::create($data);
        return redirect()->route('kriteria.sub-kriteria', $kriteria)
            ->with('success', "Kriteria {$kriteria->nama} ditambahkan. Silakan tambah skala penilaian.");
    }

    public function update(StoreKriteriaRequest $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $data = $request->validated();
        $data['has_rentang'] = $request->boolean('has_rentang');
        $kriteria->update($data);
        return redirect()->route('kriteria.index')
            ->with('success', "Kriteria {$kriteria->nama} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        if ($kriteria->periodeKriteria()->exists()) {
            return back()->with('error',
                "Kriteria {$kriteria->nama} tidak dapat dihapus karena sudah digunakan di periode penilaian."
            );
        }
        $nama = $kriteria->nama;
        $kriteria->delete();
        return redirect()->route('kriteria.index')
            ->with('success', "Kriteria {$nama} beserta sub-kriterianya berhasil dihapus.");
    }

    // ── Sub-Kriteria via menu sidebar ─────────────────────────

    /**
     * Tampilkan semua sub-kriteria dari semua kriteria
     * Diakses dari menu sidebar → Sub-Kriteria
     */
    public function subKriteriaAll()
    {
        $kriteria = Kriteria::with('subKriteria')->orderBy('nama')->get();
        return view('sub_kriteria.all', compact('kriteria'));
    }

    // ── Sub-Kriteria via halaman Kriteria ─────────────────────

    public function subKriteriaIndex($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->load('subKriteria');
        return view('sub_kriteria.index', compact('kriteria'));
    }

    public function subKriteriaStore(Request $request, $id)
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
        return back()->with('success', 'Skala penilaian berhasil ditambahkan.');
    }

    public function subKriteriaUpdate(Request $request, $kriteriaId, SubKriteria $subKriteria)
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
        return back()->with('success', 'Skala penilaian berhasil diperbarui.');
    }

    public function subKriteriaDestroy($kriteriaId, SubKriteria $subKriteria)
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