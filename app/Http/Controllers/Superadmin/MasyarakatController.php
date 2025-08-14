<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Masyarakat;
use Illuminate\Http\Request;

class MasyarakatController extends Controller
{
    // Menampilkan daftar masyarakat / data penjualan
    public function index(Request $request)
    {
        $query = Masyarakat::query();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $masyarakat = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('superadmin.masyarakat.page', compact('masyarakat'));
    }

    // Menampilkan detail masyarakat / data penjualan
    public function detail($id)
    {
        $masyarakat = Masyarakat::findOrFail($id);
        return view('superadmin.masyarakat.detail', compact('masyarakat'));
    }
}
