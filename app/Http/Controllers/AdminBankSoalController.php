<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankSoal;
use App\Models\MapingMapel;
use App\Models\DataUjian;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Log;

class AdminBankSoalController extends Controller
{
    public function index()
    {
        try {
            // Ambil data mapping hanya dari ujian yang aktif
            $mapingMapels = MapingMapel::whereHas('dataUjian', function ($query) {
                $query->where('status', true);
            })
                ->with(['guru', 'dataUjian.tahunPelajaran'])
                ->get();

            // **Transformasi data untuk tampilan di view**
            $mapingMapelsList = [];

            foreach ($mapingMapels as $maping) {
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true) ?? [];

                foreach ($mapelKelasData as $data) {
                    $mapelId = $data['mata_pelajaran_id'];

                    // Ambil nama mata pelajaran
                    $mapelNama = MataPelajaran::where('id', $mapelId)->value('nama_mapel') ?? 'Unknown Mapel';

                    // Ambil bank soal berdasarkan pencarian LIKE (karena JSON tidak bisa di-query langsung)
                    $bankSoal = BankSoal::where('guru_id', $maping->guru_id)
                        ->where('mata_pelajaran_id', 'LIKE', '%' . $mapelId . '%') // Pencarian fleksibel dalam JSON
                        ->first();

                    // Ambil daftar kelas dari `bank_soals`
                    $kelasList = [];
                    if ($bankSoal) {
                        $kelasData = json_decode($bankSoal->mata_pelajaran_id, true);
                        $kelasIds = $kelasData['kelas_id'] ?? []; // Pastikan ini array

                        if (!is_array($kelasIds)) {
                            $kelasIds = []; // Jika bukan array, jadikan array kosong
                        }

                        $kelasList = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas')->toArray();
                    }

                    // **Tambahkan ke daftar tampilan**
                    $mapingMapelsList[] = (object) [
                        'guru_nama' => $maping->guru->Nama,
                        'mata_pelajaran_nama' => $mapelNama,
                        'kelas_list' => !empty($kelasList) ? implode(', ', $kelasList) : '<span class="badge bg-warning">Guru belum mengatur kelas</span>',
                        'sudah_upload' => $bankSoal ? true : false,
                        'file_soal' => $bankSoal ? $bankSoal->file_soal : null,
                        'bank_soal_id' => $bankSoal ? $bankSoal->id : null,
                    ];
                }
            }

            return view('admin.BankSoal.index', compact('mapingMapelsList'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data Bank Soal untuk admin: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }


    public function lihatZip($id)
    {
        try {
            // Ambil data bank soal berdasarkan ID
            $bankSoal = BankSoal::findOrFail($id);

            // Path file ZIP
            $zipPath = storage_path("app/public/" . $bankSoal->file_soal);

            // Periksa apakah file ada
            if (!file_exists($zipPath)) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan.']);
            }

            // Buka ZIP dan ambil isi file
            $zip = new \ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $fileTree = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileName = $zip->getNameIndex($i);
                    $this->addToTree($fileTree, explode('/', $fileName));
                }
                $zip->close();

                return response()->json(['success' => true, 'fileTree' => $fileTree]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal membuka file ZIP.']);
        } catch (\Exception $e) {
            Log::error('Error membaca ZIP: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan.']);
        }
    }

    // Fungsi rekursif untuk membuat struktur folder
    private function addToTree(&$tree, $pathParts)
    {
        if (empty($pathParts)) {
            return;
        }

        $current = array_shift($pathParts);
        if (!isset($tree[$current])) {
            $tree[$current] = [];
        }

        $this->addToTree($tree[$current], $pathParts);
    }
}
