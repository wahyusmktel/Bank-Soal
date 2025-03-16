<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Imports\MataPelajaranImport;
use Maatwebsite\Excel\Facades\Excel;

class AdminMataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');

            // Query data mata pelajaran
            $mataPelajarans = MataPelajaran::query();

            // Filter berdasarkan nama mata pelajaran
            if (!empty($search)) {
                $mataPelajarans->where('nama_mapel', 'LIKE', "%$search%");
            }

            // Paginasi (10 data per halaman)
            $mataPelajarans = $mataPelajarans->paginate(10);

            // Kirim data ke view
            return view('admin.MataPelajaran.index', compact('mataPelajarans', 'search'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data mata pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function import(Request $request)
    {
        try {
            // Validasi file
            $request->validate([
                'file' => 'required|mimes:xlsx,csv|max:2048',
            ]);

            // Import file
            Excel::import(new MataPelajaranImport, $request->file('file'));

            return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Data Mata Pelajaran berhasil diimport!');
        } catch (\Exception $e) {
            Log::error('Error saat mengimport data mata pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengimport data.');
        }
    }
}
