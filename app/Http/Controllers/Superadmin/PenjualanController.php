<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masyarakat;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Masyarakat::with(['user.userProfile', 'polres', 'polsek']);

        $filters = $request->filters ?? [];

        // Filter Nama
        if (in_array('nama', $filters) && $request->filled('nama')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->nama . '%'));
        }

        // Filter Pangkat
        if (in_array('pangkat', $filters) && $request->filled('pangkat')) {
            $query->whereHas('user.userProfile', fn($q) => $q->where('pangkat', 'like', '%' . $request->pangkat . '%'));
        }

        // Filter Jabatan
        if (in_array('jabatan', $filters) && $request->filled('jabatan')) {
            $query->whereHas('user.userProfile', fn($q) => $q->where('jabatan', 'like', '%' . $request->jabatan . '%'));
        }

        // Filter Polres
        if (in_array('polres', $filters) && $request->filled('polres')) {
            $query->whereHas('polres', fn($q) => $q->where('nama', 'like', '%' . $request->polres . '%'));
        }

        // Filter Polsek
        if (in_array('polsek', $filters) && $request->filled('polsek')) {
            $query->whereHas('polsek', fn($q) => $q->where('nama', 'like', '%' . $request->polsek . '%'));
        }

        // Filter tanggal (range Flatpickr tanpa jam)
        if (in_array('tanggal', $filters) && $request->filled('tanggal')) {
            $dates = explode(' to ', $request->tanggal);
            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';
                $query->whereBetween('created_at', [$start, $end]);
            } else {
                $query->whereDate('created_at', trim($dates[0]));
            }
        }

        // Filter jumlah distribusi
        if (in_array('jumlah', $filters) && $request->filled('jumlah')) {
            $query->where('jumlah_beras', $request->jumlah);
        }

        $totalDistribusi = $query->sum('jumlah_beras');

        $penjualan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('superadmin.penjualan.index', [
            'penjualan' => $penjualan,
            'totalDistribusi' => $totalDistribusi,
            'selectedFilters' => $filters,
        ]);
    }
}
