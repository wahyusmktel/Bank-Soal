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
}
