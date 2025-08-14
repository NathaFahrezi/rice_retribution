<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masyarakat;
use App\Models\User;
use App\Models\Polres;
use App\Models\Polsek;

class PenjualanController extends Controller
{
    /**
     * Menampilkan halaman awal penjualan.
     */
    public function index()
    {
        $polresList = Polres::all();
        $polsekList = collect();
        $userList = collect();
        $dataPenjualan = collect();

        return view('superadmin.penjualan.index', compact(
            'polresList',
            'polsekList',
            'userList',
            'dataPenjualan'
        ));
    }

    /**
     * Filter data penjualan berdasarkan polres, polsek, user, dan rentang tanggal.
     */
    public function filter(Request $request)
    {
        $query = Masyarakat::with(['user.profile','polres','polsek']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
}

        // Filter Polres
        if ($request->filled('polres_id')) {
            $query->where('polres_id', $request->polres_id);
        }

        // Filter Polsek
        if ($request->filled('polsek_id')) {
            $query->where('polsek_id', $request->polsek_id);
        }

        // Filter User
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $dataPenjualan = $query->get();

        // Ambil list dropdown
        $polresList = Polres::all();
        $polsekList = $request->filled('polres_id')
            ? Polsek::where('polres_id', $request->polres_id)->get()
            : collect();
        $userList = $request->filled('polsek_id')
            ? User::whereHas('profile', function($q) use ($request) {
                $q->where('polsek_id', $request->polsek_id);
            })->get()
            : collect();

        // Jika request via AJAX, return partial view
        if ($request->ajax()) {
            return response()->json([
                'html' => view('superadmin.penjualan.table', compact('dataPenjualan'))->render()
            ]);
        }

        return view('superadmin.penjualan.index', compact(
            'dataPenjualan',
            'polresList',
            'polsekList',
            'userList'
        ));
    }

    /**
     * Ambil data Polsek berdasarkan Polres (AJAX).
     */
    public function getPolsek($polres_id)
    {
        $polsek = Polsek::where('polres_id', $polres_id)->get();
        return response()->json($polsek);
    }

    /**
     * Ambil data User berdasarkan Polsek (AJAX).
     */
    public function getUsers($polsek_id)
    {
        $users = User::whereHas('profile', function($q) use ($polsek_id) {
            $q->where('polsek_id', $polsek_id);
        })->get();

        return response()->json($users);
    }
}
