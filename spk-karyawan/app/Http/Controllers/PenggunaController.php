<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = User::with('karyawan')->orderBy('username')->paginate(15);
        return view('pengguna.index', compact('pengguna'));
    }

    public function create(Request $request)
    {
        $karyawan = Karyawan::whereDoesntHave('user')->orderBy('nama')->get();
        $selectedKaryawanId = $request->query('karyawan_id');
        return view('pengguna.create', compact('karyawan', 'selectedKaryawanId'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $allowedRoles = ($authUser && $authUser->role === 'direktur') ? 'admin,direktur,karyawan' : 'admin,karyawan';

        $data = $request->validate([
            'username'    => ['required', 'string', 'max:100', 'unique:users,username'],
            'password'    => ['required', Password::min(6)],
            'role'        => ['required', 'in:'.$allowedRoles],
            'karyawan_id' => ['nullable', 'exists:karyawan,id'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'password.min'    => 'Password minimal 6 karakter.',
            'role.in'         => 'Role tidak valid.',
        ]);

        if ($data['role'] === 'karyawan' && empty($data['karyawan_id'])) {
            return back()
                ->withErrors(['karyawan_id' => 'Pilih data karyawan untuk role karyawan.'])
                ->withInput();
        }

        User::create(array_merge($data, [
            'password' => Hash::make($data['password']),
        ]));

        $redirect = $request->input('redirect', 'pengguna');
        return redirect()->route($redirect === 'karyawan' ? 'karyawan.index' : 'pengguna.index')
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        $karyawan = Karyawan::where(function ($q) use ($pengguna) {
            $q->whereDoesntHave('user')
              ->orWhere('id', $pengguna->karyawan_id);
        })->orderBy('nama')->get();

        return view('pengguna.edit', compact('pengguna', 'karyawan'));
    }

    public function update(Request $request, User $pengguna)
    {
        $authUser = Auth::user();
        $allowedRoles = ($authUser && $authUser->role === 'direktur') ? 'admin,direktur,karyawan' : 'admin,karyawan';

        $data = $request->validate([
            'username'    => ['required', 'string', 'max:100', 'unique:users,username,' . $pengguna->id],
            'role'        => ['required', 'in:'.$allowedRoles],
            'karyawan_id' => ['nullable', 'exists:karyawan,id'],
            'password'    => ['nullable', Password::min(6)],
        ], ['role.in' => 'Role tidak valid.']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $pengguna->delete();
        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}