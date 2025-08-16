<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Polres;

class SuperAdminController extends Controller
{
    public function index()
    {
        $polresList = Polres::all(); // ambil semua polres
        return view('superadmin.dashboard', compact('polresList'));
    }
    
    public function resetPage()
    {
        return view('superadmin.ubah');
    }

    public function resetProcess(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->back()->with('alert', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Password berhasil diubah.',
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan saat mengubah password. Silakan coba lagi.'
            ]);
        }
    }
}
