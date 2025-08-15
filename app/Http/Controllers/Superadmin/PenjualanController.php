<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masyarakat;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query dengan relasi
        $query = Masyarakat::with(['user.userProfile', 'polres', 'polsek']);

        // Ambil filter yang dipilih
        $filters = $request->filters ?? [];

        // Filter Nama
        if (in_array('nama', $filters) && $request->filled('nama')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama . '%');
            });
        }

        // Filter NRP
        if (in_array('nrp', $filters) && $request->filled('nrp')) {
            $query->whereHas('user.userProfile', function ($q) use ($request) {
                $q->where('nrp', 'like', '%' . $request->nrp . '%');
            });
        }

        // Filter Pangkat
        if (in_array('pangkat', $filters) && $request->filled('pangkat')) {
            $query->whereHas('user.userProfile', function ($q) use ($request) {
                $q->where('pangkat', 'like', '%' . $request->pangkat . '%');
            });
        }

        // Filter Jabatan
        if (in_array('jabatan', $filters) && $request->filled('jabatan')) {
            $query->whereHas('user.userProfile', function ($q) use ($request) {
                $q->where('jabatan', 'like', '%' . $request->jabatan . '%');
            });
        }

        // Filter Polres
        if (in_array('polres', $filters) && $request->filled('polres')) {
            $query->whereHas('polres', function ($q) use ($request) {
                $q->where('nama_polres', 'like', '%' . $request->polres . '%');
            });
        }

        // Filter Polsek
        if (in_array('polsek', $filters) && $request->filled('polsek')) {
            $query->whereHas('polsek', function ($q) use ($request) {
                $q->where('nama_polsek', 'like', '%' . $request->polsek . '%');
            });
        }

        // Filter tanggal range (Flatpickr)
        if (in_array('tanggal', $filters) && $request->filled('tanggal')) {
            $dates = explode(' to ', $request->tanggal);
            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } else {
                // Jika hanya satu tanggal dipilih
                $query->whereDate('created_at', $dates[0]);
            }
        }

        // Filter jumlah distribusi
        if (in_array('jumlah', $filters) && $request->filled('jumlah')) {
            $query->where('jumlah_beras', $request->jumlah);
        }

        // Hitung total distribusi berdasarkan data yang difilter
        $totalDistribusi = $query->sum('jumlah_beras');

        // Ambil data hanya jika ada filter yang diisi, tetapkan query string untuk paginate
        $penjualan = !empty($filters)
            ? $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
            : collect();

        return view('superadmin.penjualan.index', [
            'penjualan' => $penjualan,
            'totalDistribusi' => $totalDistribusi,
            'selectedFilters' => $filters,
        ]);
    }
}
