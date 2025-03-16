<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminKelasController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');

            // Query data kelas
            $kelas = Kelas::query();

            // Filter berdasarkan nama kelas atau tingkat kelas
            if (!empty($search)) {
                $kelas->where('nama_kelas', 'LIKE', "%$search%")
                    ->orWhere('tingkat_kelas', 'LIKE', "%$search%");
            }

            // Paginasi (10 data per halaman)
            $kelas = $kelas->paginate(10);

            // Kirim data ke view
            return view('admin.Kelas.index', compact('kelas', 'search'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data kelas: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_kelas' => 'required|string|max:255',
                'tingkat_kelas' => 'required|integer|in:10,11,12',
            ]);

            // Simpan data kelas
            Kelas::create([
                'id' => Str::uuid(),
                'nama_kelas' => $request->nama_kelas,
                'tingkat_kelas' => $request->tingkat_kelas,
                'status' => true, // Default true saat insert
            ]);

            return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan kelas: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }
}
