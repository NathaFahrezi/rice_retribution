<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masyarakat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasyarakatController extends Controller
{
    public function index(Request $request)
    {
        $masyarakat = Masyarakat::query()
        ->select('id',  'jumlah_beras', 'created_at','foto_ktp')
        ->where('created_by', Auth::id())
        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();
        
        return view('user.masyarakat.page',compact('masyarakat'));
    }

    public function showPage(){
        return view('user.masyarakat.tambah');
    }

    public function store(Request $request){
        $request->validate([
            // 'nama' => 'required|string|max:255',
            // 'nik' => 'required|digits:16|numeric',
            // 'alamat' => 'required|string|max:255',
            'jumlah_beras' => 'required|integer|min:1',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            // 'nama.required' => 'Nama wajib diisi.',
            // 'nama.string' => 'Nama harus berupa teks.',
            // 'nama.max' => 'Nama maksimal 255 karakter.',
        
            // 'nik.required' => 'NIK wajib diisi.',
            // 'nik.digits' => 'NIK harus terdiri dari tepat 16 digit.',
            // 'nik.numeric' => 'NIK harus berupa angka.',
        
            // 'alamat.required' => 'Alamat wajib diisi.',
            // 'alamat.string' => 'Alamat harus berupa teks.',
            // 'alamat.max' => 'Alamat maksimal 255 karakter.',
        
            'jumlah_beras.required' => 'Jumlah beras wajib diisi.',
            'jumlah_beras.integer' => 'Jumlah beras harus berupa angka bulat.',
            'jumlah_beras.min' => 'Jumlah beras minimal 1.',
        
            'foto_ktp.image' => 'Foto KTP harus berupa file gambar.',
            'foto_ktp.mimes' => 'Format foto KTP harus jpeg, png, jpg, atau gif.',
            'foto_ktp.max' => 'Ukuran Foto KTP maksimal 10 MB.',
        ]);

        try {

            DB::beginTransaction();

            // Simpan foto jika ada
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                \Log::info('File foto_ktp ditemukan', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size_kb' => $file->getSize() / 1024
                ]);
    
                // Nama file aman
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $file->getClientOriginalName());
    
                // Pastikan folder ada
                $destinationPath = public_path('uploads/ktp');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                    \Log::info("Folder uploads/ktp dibuat");
                }
    
                // Simpan file baru
                $file->move($destinationPath, $filename);
                \Log::info("File baru disimpan: {$filename}");
                $uploadedFiles[] = $destinationPath . '/' . $filename;
                $fotoPath = $filename;
    
            } else {
                \Log::warning('Tidak ada file foto_ktp dikirim di request');
            }

            // Simpan data ke database
            Masyarakat::create([
                'polres_id' => Auth::user()->profile->polres_id,
                'polsek_id' => Auth::user()->profile->polsek_id,
                'created_by' => Auth::id(),
                'jumlah_beras' => $request->jumlah_beras,
                'foto_ktp' => $fotoPath,
            ]);
            DB::commit();
            return redirect('/user/masyarakat')->with('alert', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data Masyarakat Berhasil Di Tambah!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Gagal tambah data', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            foreach ($uploadedFiles as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan saat menambah data. Silakan coba lagi.'
            ]);
        }
    }

    public function detailPage($id){
        $masyarakat = Masyarakat::where('id', $id)
        ->where('created_by', Auth::id())
        ->firstOrFail();
        return view('user.masyarakat.detail',compact('masyarakat'));
    }

    public function destroy($id){
        try {
            $masyarakat = Masyarakat::where('id', $id)
                ->where('created_by', Auth::id())
                ->firstOrFail();
    
            // Hapus file foto_ktp jika ada
            if ($masyarakat->foto_ktp) {
                $filePath = public_path('uploads/ktp/' . $masyarakat->foto_ktp);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
    
            $masyarakat->delete();
    
            return redirect()->route('user.masyarakat')->with('alert', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data masyarakat berhasil dihapus.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('user.masyarakat')->with('alert', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Data tidak ditemukan atau Anda tidak berhak menghapus data ini.',
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('user.masyarakat')->with('alert', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.',
            ]);
        }
    }

    public function showEdit($id){
        $masyarakat = Masyarakat::where('id', $id)
        ->where('created_by', Auth::id())
        ->firstOrFail();
        return view('user.masyarakat.edit',compact('masyarakat'));
    }

    public function update(Request $request, $id){
        $request->validate([
            // 'nama' => 'required|string|max:255',
            // 'nik' => 'required|digits:16|numeric',
            // 'alamat' => 'required|string|max:255',
            'jumlah_beras' => 'required|integer|min:1',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            // 'nama.required' => 'Nama wajib diisi.',
            // 'nama.string' => 'Nama harus berupa teks.',
            // 'nama.max' => 'Nama maksimal 255 karakter.',
        
            // 'nik.required' => 'NIK wajib diisi.',
            // 'nik.digits' => 'NIK harus terdiri dari tepat 16 digit.',
            // 'nik.numeric' => 'NIK harus berupa angka.',
        
            // 'alamat.required' => 'Alamat wajib diisi.',
            // 'alamat.string' => 'Alamat harus berupa teks.',
            // 'alamat.max' => 'Alamat maksimal 255 karakter.',
        
            'jumlah_beras.required' => 'Jumlah beras wajib diisi.',
            'jumlah_beras.integer' => 'Jumlah beras harus berupa angka bulat.',
            'jumlah_beras.min' => 'Jumlah beras minimal 1.',
        
            'foto_ktp.image' => 'Foto KTP harus berupa file gambar.',
            'foto_ktp.mimes' => 'Format foto KTP harus jpeg, png, jpg, atau gif.',
            'foto_ktp.max' => 'Ukuran Foto KTP maksimal 10 MB.',
        ]);

        try {
            DB::beginTransaction();

            $masyarakat = Masyarakat::findOrFail($id);

            $masyarakat->jumlah_beras = $request->jumlah_beras;

            // 4. Cek file
        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            \Log::info('File foto_ktp ditemukan', [
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size_kb' => $file->getSize() / 1024
            ]);

            // Nama file aman
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $file->getClientOriginalName());

            // Pastikan folder ada
            $destinationPath = public_path('uploads/ktp');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
                \Log::info("Folder uploads/ktp dibuat");
            }

            // Hapus file lama kalau ada
            $oldFile = public_path('uploads/ktp/' . $masyarakat->foto_ktp);
            if ($masyarakat->foto_ktp && file_exists($oldFile)) {
                unlink($oldFile);
                \Log::info("File lama dihapus: {$oldFile}");
            }

            // Simpan file baru
            $file->move($destinationPath, $filename);
            \Log::info("File baru disimpan: {$filename}");
            $uploadedFiles[] = $destinationPath . '/' . $filename;

            $masyarakat->foto_ktp = $filename;

            } else {
                \Log::warning('Tidak ada file foto_ktp dikirim di request');
            }

            // 5. Simpan perubahan
            $masyarakat->save();
            \Log::info("Profile berhasil disimpan, foto_ktp: {$masyarakat->foto_ktp}");


            DB::commit();

            return redirect()->route('user.masyarakat')->with('alert', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Data Masyarakat berhasil diperbarui!'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Gagal update profil', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            foreach ($uploadedFiles as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Terjadi kesalahan saat update data. Silakan coba lagi.'
            ]);
        }
        
    }
}
