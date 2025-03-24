<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankSoal;
use App\Models\MapingMapel;
use App\Models\Guru;
use App\Models\DataUjian;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\ValidasiSoal;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $jumlahGuru = Guru::count();
        $jumlahSoal = BankSoal::count();
        $jumlahMapel = MataPelajaran::count();
        $jumlahBelumValidasi = ValidasiSoal::where('status', true)
            ->where(function ($q) {
                $q->whereNull('soal')->orWhere('soal', '[]')->orWhere('soal', '{}');
            })->count();

        // Tahun Pelajaran Aktif
        $tahunAktif = TahunPelajaran::where('status', true)->first();

        // Ujian Aktif
        $ujianAktif = DataUjian::where('status', true)->first();

        return view('admin.dashboard', compact(
            'jumlahGuru',
            'jumlahSoal',
            'jumlahMapel',
            'jumlahBelumValidasi',
            'tahunAktif',
            'ujianAktif'
        ));
    }
}
