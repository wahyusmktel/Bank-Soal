<?php

namespace App\Http\Controllers;

use App\Models\DataUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\TahunPelajaran;

class AdminDataUjianController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');

            // Query data ujian
            $dataUjians = DataUjian::with('tahunPelajaran');

            // Filter berdasarkan nama ujian
            if (!empty($search)) {
                $dataUjians->where('nama_ujian', 'LIKE', "%$search%");
            }

            // Paginasi (10 data per halaman)
            $dataUjians = $dataUjians->paginate(10);

            // Ambil daftar tahun pelajaran untuk dropdown di modal
            $tahunPelajarans = TahunPelajaran::orderBy('nama_tahun', 'desc')->get();

            // Kirim data ke view
            return view('admin.DataUjian.index', compact('dataUjians', 'search', 'tahunPelajarans'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data ujian: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tahun_pelajaran_id' => 'required|uuid|exists:tahun_pelajarans,id',
                'nama_ujian' => 'required|string|max:255',
                'tgl_mulai' => 'required|date_format:Y-m-d\TH:i',
                'tgl_akhir' => 'required|date_format:Y-m-d\TH:i|after_or_equal:tgl_mulai',
            ]);

            // Simpan data
            DataUjian::create([
                'id' => Str::uuid(),
                'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                'nama_ujian' => $request->nama_ujian,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
                'status' => false, // Default false
            ]);

            return redirect()->route('admin.data-ujian.index')->with('success', 'Data Ujian berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan Data Ujian: ' . $e->getMessage());
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
                DataUjian::query()->update(['status' => false]);
            }

            // Perbarui data yang dipilih
            DataUjian::where('id', $request->id)->update([
                'status' => $request->status
            ]);

            return response()->json(['success' => true, 'message' => 'Status ujian diperbarui!']);
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status ujian: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui status.'], 500);
        }
    }
}
