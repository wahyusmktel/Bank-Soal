<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AkunGuru;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminGuruController extends BaseController
{
    public function __construct()
    {
        $this->middleware('admin.auth'); // Pastikan hanya admin yang bisa mengakses
    }

    /**
     * Menampilkan daftar guru dengan fitur pencarian, filter, dan paginasi.
     */
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');
            $jk = $request->input('jk');
            $statusKepegawaians = Guru::whereNotNull('Status_Kepegawaian')->distinct()->pluck('Status_Kepegawaian');
            $jenisPtks = Guru::whereNotNull('Jenis_PTK')->distinct()->pluck('Jenis_PTK');
            $tugasTambahan = Guru::whereNotNull('Tugas_Tambahan')->distinct()->pluck('Tugas_Tambahan');

            // Query builder untuk pencarian dan filter
            $gurus = Guru::with('akunGuru'); // ⬅️ Load relasi akunGuru

            // Pencarian
            if (!empty($search)) {
                $gurus->where(function ($query) use ($search) {
                    $query->where('Nama', 'LIKE', "%$search%")
                        ->orWhere('NUPTK', 'LIKE', "%$search%")
                        ->orWhere('NIP', 'LIKE', "%$search%")
                        ->orWhere('HP', 'LIKE', "%$search%")
                        ->orWhere('Email', 'LIKE', "%$search%")
                        ->orWhere('NIK', 'LIKE', "%$search%");
                });
            }

            // Filter
            if (!empty($jk)) {
                $gurus->where('JK', $jk);
            }
            if ($request->filled('status_kepegawaian')) {
                $gurus->where('Status_Kepegawaian', $request->status_kepegawaian);
            }
            if ($request->filled('jenis_ptk')) {
                $gurus->where('Jenis_PTK', $request->jenis_ptk);
            }
            if ($request->filled('tugas_tambahan')) {
                $gurus->where('Tugas_Tambahan', $request->tugas_tambahan);
            }

            // Paginasi (10 data per halaman)
            $gurus = $gurus->paginate(10);

            // Tampilkan ke view dengan data guru
            return view('admin.guru.index', compact('gurus', 'search', 'jk', 'statusKepegawaians', 'jenisPtks', 'tugasTambahan'));
        } catch (\Exception $e) {
            // Log error dan tampilkan pesan kesalahan
            Log::error('Error saat mengambil data guru: ' . $e->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat memuat data.');
            return back();
        }
    }

    public function import(Request $request)
    {
        try {
            // Validasi file
            $request->validate([
                'file' => 'required|mimes:xlsx,csv|max:2048',
            ]);

            // Jalankan import dan simpan hasilnya ke variabel
            $import = new GuruImport();
            Excel::import($import, $request->file('file'));

            // Ambil jumlah data yang berhasil ditambahkan dan diperbarui
            $added = $import->getAddedCount();
            $updated = $import->getUpdatedCount();

            // Buat pesan sukses
            Alert::success('Berhasil', "Import Selesai! $added Data Ditambahkan, $updated Data Diperbarui.");
            return redirect()->route('admin.guru.index');
        } catch (\Exception $e) {
            Log::error('Error saat mengimport data guru: ' . $e->getMessage());
            Alert::error('Gagal', 'Terjadi kesalahan saat mengimport data.');
            return back();
        }
    }

    public function show($id)
    {
        try {
            // Ambil data guru berdasarkan ID
            $guru = Guru::findOrFail($id);

            return view('admin.guru.show', compact('guru'));
        } catch (\Exception $e) {
            Log::error('Error saat menampilkan detail guru: ' . $e->getMessage());
            Alert::error('Gagal', 'Data guru tidak ditemukan.');
            return redirect()->route('admin.guru.index');
        }
    }

    public function generateAkun(Request $request)
    {
        $guruIds = $request->guru_ids;

        if (!$guruIds) {
            return response()->json(['message' => 'Tidak ada guru yang dipilih'], 400);
        }

        foreach ($guruIds as $guruId) {
            $guru = Guru::find($guruId);

            if ($guru) {
                $randomPassword = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $email = $guru->Email ?? $guru->NIK . '@smktelkom-lpg.sch.id';

                AkunGuru::updateOrCreate(
                    ['guru_id' => $guru->id],
                    [
                        'username' => $guru->NIK,
                        'password' => Hash::make($randomPassword),
                        'email' => $email,
                    ]
                );

                // Simpan password dalam tabel gurus
                $guru->update(['password' => $randomPassword]);
            }
        }

        return response()->json(['message' => 'Akun guru berhasil dibuat/diperbarui!']);
    }
}
