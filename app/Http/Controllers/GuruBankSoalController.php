<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BankSoal;
use Illuminate\Support\Facades\Log;

class GuruBankSoalController extends Controller
{
    public function index()
    {
        try {
            // Ambil ID guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Ambil data bank soal berdasarkan guru yang sedang login
            $bankSoals = BankSoal::where('guru_id', $guruId)->orderBy('created_at', 'desc')->paginate(10);

            // Kirim data ke view
            return view('guru.BankSoal.index', compact('bankSoals'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data bank soal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'file_soal' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);

            // Ambil ID guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Simpan file soal ke storage
            $filePath = $request->file('file_soal')->store('bank-soal', 'public');

            // Simpan data ke database
            BankSoal::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'guru_id' => $guruId,
                'file_soal' => $filePath,
                'status' => true, // Otomatis aktif
            ]);

            return redirect()->route('guru.bank-soal.index')->with('success', 'Bank Soal berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan Bank Soal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan Bank Soal.');
        }
    }
}
