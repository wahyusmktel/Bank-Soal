<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankSoal;
use Illuminate\Support\Collection;
use App\Models\MapingMapel;
use App\Models\Guru;
use App\Models\DataUjian;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\ValidasiSoal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $jumlahGuru = Guru::count();

        // Hitung total kombinasi unik mapel+kelas dari MapingMapel
        $jumlahSoal = MapingMapel::all()
            ->flatMap(function ($maping) {
                $decoded = json_decode($maping->mata_pelajaran_id, true);
                return is_array($decoded)
                    ? collect($decoded)->map(function ($item) {
                        $kelas = $item['kelas_id'] ?? [];
                        sort($kelas);
                        return $item['mata_pelajaran_id'] . '|' . implode(',', $kelas);
                    })
                    : collect();
            })
            ->unique()
            ->count();

        $jumlahMapel = MataPelajaran::count();

        // Ambil semua bank soal yang valid dan generate key kombinasi unik
        $uploadedKeys = BankSoal::all()
            ->filter(function ($soal) {
                // Decode dua kali karena tersimpan sebagai string json dalam string
                $firstDecode = json_decode($soal->getRawOriginal('mata_pelajaran_id'), true);
                if (is_string($firstDecode)) {
                    $data = json_decode($firstDecode, true); // decode kedua
                } else {
                    $data = $firstDecode;
                }

                return is_array($data)
                    && isset($data['mata_pelajaran_id'])
                    && isset($data['kelas_id'])
                    && is_array($data['kelas_id']);
            })
            ->map(function ($soal) {
                $firstDecode = json_decode($soal->getRawOriginal('mata_pelajaran_id'), true);
                if (is_string($firstDecode)) {
                    $data = json_decode($firstDecode, true);
                } else {
                    $data = $firstDecode;
                }

                $kelasIds = $data['kelas_id'];
                sort($kelasIds);
                return $soal->guru_id . '|' . $soal->data_ujian_id . '|' . $data['mata_pelajaran_id'] . '|' . implode(',', $kelasIds);
            })
            ->unique()
            ->values()
            ->toArray();

        // Debug log
        Log::info('✅ Uploaded Keys:', $uploadedKeys);

        // Ambil semua kombinasi unik dari MapingMapel
        $kombinasiUnik = MapingMapel::all()
            ->flatMap(function ($maping) {
                $decoded = json_decode($maping->mata_pelajaran_id, true);
                if (!is_array($decoded)) return collect();

                return collect($decoded)->map(function ($item) use ($maping) {
                    $kelas = $item['kelas_id'] ?? [];
                    sort($kelas);
                    return $maping->guru_id . '|' . $maping->data_ujian_id . '|' . $item['mata_pelajaran_id'] . '|' . implode(',', $kelas);
                });
            })->unique();

        Log::info('📘 Kombinasi Unik Maping:', $kombinasiUnik->toArray());

        // Hitung yang sudah upload (dengan key matching)
        $jumlahSudahUpload = $kombinasiUnik
            ->filter(fn($key) => in_array($key, $uploadedKeys))
            ->count();

        $jumlahBelumUpload = $kombinasiUnik
            ->reject(fn($key) => in_array($key, $uploadedKeys))
            ->count();

        $guruBelumUpload = [];

        MapingMapel::with(['guru', 'dataUjian', 'tahunPelajaran'])
            ->get()
            ->each(function ($maping) use (&$guruBelumUpload, $uploadedKeys) {
                $decoded = json_decode($maping->mata_pelajaran_id, true);
                if (!is_array($decoded)) return;

                foreach ($decoded as $item) {
                    $kelas = $item['kelas_id'] ?? [];
                    sort($kelas);
                    $key = $maping->guru_id . '|' . $maping->data_ujian_id . '|' . $item['mata_pelajaran_id'] . '|' . implode(',', $kelas);

                    if (!in_array($key, $uploadedKeys)) {
                        $guruBelumUpload[] = [
                            'nama_guru' => optional($maping->guru)->Nama ?? 'Tidak diketahui',
                            'mapel' => MataPelajaran::find($item['mata_pelajaran_id'])->nama_mapel ?? 'Unknown Mapel',
                            'kelas' => Kelas::whereIn('id', $kelas)->pluck('nama_kelas')->implode(', ')
                        ];
                    }
                }
            });

        // Tahun Pelajaran Aktif
        $tahunAktif = TahunPelajaran::where('status', true)->first();

        // Ujian Aktif
        $ujianAktif = DataUjian::where('status', true)->first();

        return view('admin.dashboard', compact(
            'jumlahGuru',
            'jumlahSoal',
            'jumlahMapel',
            'jumlahSudahUpload',
            'jumlahBelumUpload',
            'tahunAktif',
            'ujianAktif',
            'guruBelumUpload'
        ));
    }
}
