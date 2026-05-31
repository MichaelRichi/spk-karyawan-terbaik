<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function show()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;
        return view('profil.show', compact('user', 'karyawan'));
    }

    public function edit()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;
        return view('profil.edit', compact('user', 'karyawan'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'username'         => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id],
            'password_baru'    => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_lama'    => ['nullable', 'string'],
        ];

        // Jika terhubung karyawan, validasi data karyawan
        if ($user->karyawan) {
            $rules['no_telepon'] = ['nullable', 'string', 'max:20'];
            $rules['alamat']     = ['nullable', 'string', 'max:500'];
        }

        $request->validate($rules);

        // Ganti password jika diisi
        if ($request->filled('password_baru')) {
            if (!$request->filled('password_lama') || !Hash::check($request->password_lama, $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($request->password_baru);
        }

        $user->username = $request->username;
        $user->save();

        // Update data karyawan jika ada
        if ($user->karyawan && ($request->filled('no_telepon') || $request->filled('alamat'))) {
            $user->karyawan->update([
                'no_telepon' => $request->no_telepon,
                'alamat'     => $request->alamat,
            ]);
        }

        return redirect()->route('profil.show')->with('success', 'Profil berhasil diperbarui.');
    }
}