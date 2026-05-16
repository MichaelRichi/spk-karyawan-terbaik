<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKaryawanRequest;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $karyawan = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(StoreKaryawanRequest $request)
    {
        $karyawan = Karyawan::create($request->validated());

        return redirect()->route('karyawan.index')
            ->with('success', "Karyawan {$karyawan->nama} berhasil ditambahkan.");
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(StoreKaryawanRequest $request, Karyawan $karyawan)
    {
        $karyawan->update($request->validated());

        return redirect()->route('karyawan.index')
            ->with('success', "Data {$karyawan->nama} berhasil diperbarui.");
    }

    public function destroy(Karyawan $karyawan)
    {
        // Cek apakah karyawan sedang dalam periode aktif
        $sedangDinilai = $karyawan->penilaian()
            ->whereHas('periode', fn($q) => $q->where('status', 'aktif'))
            ->exists();

        if ($sedangDinilai) {
            return back()->with('error',
                "Karyawan {$karyawan->nama} sedang dalam proses penilaian aktif dan tidak dapat dihapus."
            );
        }

        $nama = $karyawan->nama;
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', "Karyawan {$nama} berhasil dihapus.");
    }
}