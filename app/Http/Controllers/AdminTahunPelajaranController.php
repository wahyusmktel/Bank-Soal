<?php

namespace App\Http\Controllers;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminTahunPelajaranController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');

            // Query data tahun pelajaran
            $tahunPelajarans = TahunPelajaran::query();

            // Filter berdasarkan nama_tahun
            if (!empty($search)) {
                $tahunPelajarans->where('nama_tahun', 'LIKE', "%$search%")
                    ->orWhere('semester', 'LIKE', "%$search%");
            }

            // Paginasi (10 data per halaman)
            $tahunPelajarans = $tahunPelajarans->paginate(10);

            // Kirim data ke view
            return view('admin.TahunPelajaran.index', compact('tahunPelajarans', 'search'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data tahun pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_tahun' => 'required|string|max:255',
                'semester' => 'required|in:1,2', // Pastikan hanya 1 atau 2 yang bisa diterima
            ]);

            // Simpan data
            TahunPelajaran::create([
                'id' => Str::uuid(),
                'nama_tahun' => $request->nama_tahun,
                'semester' => $request->semester,
                'status' => false, // Default false
            ]);

            return redirect()->route('admin.tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan Tahun Pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'id' => 'required|uuid',
                'status' => 'required|boolean'
            ]);

            // Nonaktifkan semua data jika status yang dikirim adalah true
            if ($request->status) {
                TahunPelajaran::query()->update(['status' => false]);
            }

            // Perbarui data yang dipilih
            TahunPelajaran::where('id', $request->id)->update([
                'status' => $request->status
            ]);

            return response()->json(['success' => true, 'message' => 'Status tahun pelajaran diperbarui!']);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status tahun pelajaran: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status.'], 500);
        }
    }
}
