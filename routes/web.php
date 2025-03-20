<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBankSoalController;
use App\Http\Controllers\AdminGuruController;
use App\Http\Controllers\AdminMataPelajaranController;
use App\Http\Controllers\AdminTahunPelajaranController;
use App\Http\Controllers\AdminDataUjianController;
use App\Http\Controllers\AdminMapingController;
use App\Http\Controllers\AdminKelasController;
use App\Http\Controllers\GuruAuthController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\GuruBankSoalController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register']);

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/guru', [AdminGuruController::class, 'index'])->name('admin.guru.index');
        Route::post('/guru/import', [AdminGuruController::class, 'import'])->name('admin.guru.import');
        Route::get('/guru/{id}', [AdminGuruController::class, 'show'])->name('admin.guru.show');
        Route::post('/guru/generate-akun', [AdminGuruController::class, 'generateAkun'])->name('admin.guru.generateAkun');

        Route::get('/mata-pelajaran', [AdminMataPelajaranController::class, 'index'])->name('admin.mata-pelajaran.index');
        Route::post('/mata-pelajaran/import', [AdminMataPelajaranController::class, 'import'])->name('admin.mata-pelajaran.import');

        Route::get('/tahun-pelajaran', [AdminTahunPelajaranController::class, 'index'])->name('admin.tahun-pelajaran.index');
        Route::post('/tahun-pelajaran/store', [AdminTahunPelajaranController::class, 'store'])->name('admin.tahun-pelajaran.store');
        Route::post('/tahun-pelajaran/update-status', [AdminTahunPelajaranController::class, 'updateStatus'])->name('admin.tahun-pelajaran.updateStatus');

        Route::get('/data-ujian', [AdminDataUjianController::class, 'index'])->name('admin.data-ujian.index');
        Route::post('/data-ujian/store', [AdminDataUjianController::class, 'store'])->name('admin.data-ujian.store');
        Route::post('/data-ujian/update-status', [AdminDataUjianController::class, 'updateStatus'])->name('admin.data-ujian.updateStatus');

        Route::get('/maping-mapel', [AdminMapingController::class, 'index'])->name('admin.maping.index');
        Route::post('/maping-mapel/store', [AdminMapingController::class, 'store'])->name('admin.maping.store');
        Route::put('/maping/{id}', [AdminMapingController::class, 'update'])->name('admin.maping.update'); // Edit Data
        Route::delete('/maping-mapel/{id}', [AdminMapingController::class, 'destroy'])->name('admin.maping.destroy'); // Hapus Data

        Route::get('/kelas', [AdminKelasController::class, 'index'])->name('admin.kelas.index');
        Route::post('/kelas/store', [AdminKelasController::class, 'store'])->name('admin.kelas.store');

        Route::get('/bank-soal', [AdminBankSoalController::class, 'index'])->name('admin.bank-soal.index');
        Route::get('/bank-soal/lihat-zip/{id}', [AdminBankSoalController::class, 'lihatZip'])->name('admin.bank-soal.lihat-zip');


        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/login', [GuruAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [GuruAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [GuruAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth.guru'])->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bank-soal', [GuruBankSoalController::class, 'index'])->name('bank-soal.index');
        Route::post('/bank-soal', [GuruBankSoalController::class, 'store'])->name('bank-soal.store');
        Route::get('/bank-soal/lihat-zip/{id}', [GuruBankSoalController::class, 'lihatZip']);
        Route::get('/bank-soal/preview/{id}', [GuruBankSoalController::class, 'previewSoal']);
        Route::post('/bank-soal/validasi', [GuruBankSoalController::class, 'simpanValidasiSoal'])->name('validasi.soal');
    });
});
