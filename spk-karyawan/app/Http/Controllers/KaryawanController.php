<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKaryawanRequest;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ;
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $karyawan = $query->with('user')->orderBy('nama')->paginate(15)->withQueryString();
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
        $karyawan->update($request->only(['nama','tgl_lahir','jenis_kelamin','tgl_masuk','status','no_telepon','alamat']));

        // Sync is_active user mengikuti status karyawan
        if ($karyawan->user) {
            $karyawan->user->update([
                'is_active' => $karyawan->status === 'aktif',
            ]);
        }

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

    public function akunForm(Karyawan $karyawan)
    {
        $user = $karyawan->user;
        return view('karyawan.akun', compact('karyawan', 'user'));
    }

    public function akunStore(Request $request, Karyawan $karyawan)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', Password::min(6), 'confirmed'],
            'role'     => ['required', 'in:admin,karyawan'],
        ], ['username.unique' => 'Username sudah digunakan.']);

        User::create([
            'username'    => $data['username'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'],
            'karyawan_id' => $karyawan->id,
        ]);

        return redirect()->route('karyawan.index')
            ->with('success', "Akun untuk {$karyawan->nama} berhasil dibuat.");
    }

    public function akunUpdate(Request $request, Karyawan $karyawan)
    {
        $user = $karyawan->user;
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username,'.$user->id],
            'password' => ['nullable', Password::min(6), 'confirmed'],
            'role'     => ['required', 'in:admin,karyawan'],
        ], ['username.unique' => 'Username sudah digunakan.']);

        $update = ['username' => $data['username'], 'role' => $data['role']];
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('karyawan.index')
            ->with('success', "Akun {$karyawan->nama} berhasil diperbarui.");
    }
}