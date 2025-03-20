<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BankSoal;
use App\Models\DataUjian;
use App\Models\Kelas;
use App\Models\MapingMapel;
use App\Models\MataPelajaran;
use App\Models\ValidasiSoal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GuruBankSoalController extends Controller
{
    public function index()
    {
        try {
            // Ambil ID guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Query data mapping berdasarkan guru yang sedang login & ujian yang aktif
            $mapingMapels = MapingMapel::where('guru_id', $guruId)
                ->whereHas('dataUjian', function ($query) {
                    $query->where('status', true); // Hanya ambil ujian yang aktif
                })
                ->with(['dataUjian.tahunPelajaran'])
                ->get();

            // Optimasi data sebelum dikirim ke view
            $mapingMapels = $mapingMapels->map(function ($maping) {
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true) ?? [];

                $mapelIds = collect($mapelKelasData)->pluck('mata_pelajaran_id')->unique()->toArray();
                $kelasIds = collect($mapelKelasData)->pluck('kelas_id')->flatten()->unique()->toArray();

                // Ambil data mapel & kelas hanya sekali untuk mengurangi query
                $mapels = MataPelajaran::whereIn('id', $mapelIds)->pluck('nama_mapel', 'id')->toArray();
                $kelas = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas', 'id')->toArray();

                $maping->mapel_kelas_list = collect($mapelKelasData)->map(function ($data) use ($mapels, $kelas) {
                    return [
                        'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                        'mapel' => $mapels[$data['mata_pelajaran_id']] ?? 'Unknown Mapel',
                        // 'kelas' => collect($data['kelas_id'])->map(fn($k) => $kelas[$k] ?? 'Unknown Kelas')->implode(', '),
                        // 'kelas' => collect($data['kelas_id'])->map(fn($k) => $kelas[$k] ?? 'Unknown Kelas')->toArray(),
                        'kelas' => collect($data['kelas_id'])->map(fn($k) => [
                            'id' => $k, // Menggunakan UUID sebagai ID
                            'nama' => $kelas[$k] ?? 'Unknown Kelas' // Menampilkan Nama Kelas
                        ])->toArray(),
                    ];
                });

                return $maping;
            });

            // Ambil daftar kelas
            $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

            // Ambil daftar soal yang dimiliki oleh guru dengan pagination
            $bankSoals = BankSoal::where('guru_id', $guruId)->paginate(10);

            return view('guru.BankSoal.index', compact('mapingMapels', 'kelas', 'bankSoals'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data bank soal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            // ✅ Debugging - Log data yang dikirim oleh user
            Log::info('Data yang dikirim oleh user:', $request->all());

            // Validasi input
            $validatedData = $request->validate([
                'mata_pelajaran_id' => 'required|uuid|exists:mata_pelajarans,id',
                'kelas_id' => 'required|array|min:1',
                'kelas_id.*' => 'uuid|exists:kelas,id',
                'file_soal' => 'required|file|mimes:zip|max:5120', // Max 5MB
            ]);

            // Ambil ID guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Ambil data mapel dan kelas
            $mapelId = $validatedData['mata_pelajaran_id'];
            $kelasIds = array_values($validatedData['kelas_id']); // Pastikan berupa array numerik

            // ✅ Debugging - Log Mata Pelajaran & Kelas
            Log::info('Mata Pelajaran ID:', ['id' => $mapelId]);
            Log::info('Kelas IDs:', ['kelas' => $kelasIds]);

            // Ambil nama mata pelajaran
            $mapelNama = MataPelajaran::where('id', $mapelId)->value('nama_mapel');

            // Ambil nama kelas berdasarkan kelas_id yang dipilih
            $kelasNamaList = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas')->toArray();
            $kelasNama = implode('-', $kelasNamaList); // Format kelas jadi string "XII RPL- XI TKJ"

            // ✅ Debugging - Log Nama Mata Pelajaran dan Kelas
            Log::info('Nama Mata Pelajaran:', ['nama' => $mapelNama]);
            Log::info('Nama Kelas:', ['kelas' => $kelasNama]);

            // Ambil ujian yang memiliki status aktif dari MapingMapel
            $ujian = MapingMapel::where('guru_id', $guruId)
                ->whereHas('dataUjian', function ($query) {
                    $query->where('status', true); // Pastikan hanya ujian aktif
                })
                ->with('dataUjian.tahunPelajaran')
                ->first();

            if (!$ujian) {
                return back()->with('error', 'Tidak ada ujian aktif yang tersedia.');
            }

            // Ambil informasi dari DataUjian dan TahunPelajaran
            $dataUjian = $ujian->dataUjian;
            $tahunPelajaran = $dataUjian->tahunPelajaran->nama_tahun ?? 'Unknown Tahun';
            $semester = $dataUjian->tahunPelajaran->semester ?? 'Unknown Semester';

            // ✅ Debugging - Log Ujian, Tahun Pelajaran, dan Semester
            Log::info('Ujian Aktif:', ['nama' => $dataUjian->nama_ujian]);
            Log::info('Tahun Pelajaran:', ['nama_tahun' => $tahunPelajaran]);
            Log::info('Semester:', ['semester' => $semester]);

            // Cek apakah sudah ada soal dengan mata pelajaran dan kelas yang sama untuk ujian ini
            $existingSoal = BankSoal::where('guru_id', $guruId)
                ->whereJsonContains('mata_pelajaran_id->mata_pelajaran_id', $mapelId)
                ->whereJsonContains('mata_pelajaran_id->kelas_id', $kelasIds)
                ->exists();

            if ($existingSoal) {
                return back()->with('error', 'Soal untuk mata pelajaran dan kelas ini sudah ada.');
            }

            // Buat nama file yang unik dengan UUID
            $uniqueId = \Illuminate\Support\Str::uuid();
            $fileName = "{$dataUjian->nama_ujian}_{$tahunPelajaran}_Semester_{$semester}_{$mapelNama}_{$kelasNama}_{$uniqueId}.zip";

            // Simpan file soal ke storage dengan nama yang diformat
            $filePath = $request->file('file_soal')->storeAs('bank-soal', $fileName, 'public');

            // ✅ Debugging - Log File yang Disimpan
            Log::info('File Soal Disimpan:', ['path' => $filePath]);

            // Simpan data mata pelajaran & kelas dalam JSON
            $mapelKelasData = [
                'mata_pelajaran_id' => $mapelId,
                'kelas_id' => $kelasIds, // Menyimpan lebih dari satu kelas
            ];

            // Simpan data ke database
            BankSoal::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'guru_id' => $guruId,
                'mata_pelajaran_id' => json_encode($mapelKelasData), // Simpan sebagai JSON
                'file_soal' => $filePath,
                'status' => true, // Otomatis aktif
            ]);

            return redirect()->route('guru.bank-soal.index')->with('success', 'Bank Soal berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan Bank Soal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan Bank Soal.');
        }
    }

    public function lihatZip($id)
    {
        try {
            $bankSoal = BankSoal::findOrFail($id);
            $zipPath = storage_path('app/public/' . $bankSoal->file_soal);

            if (!file_exists($zipPath)) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $files = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileName = $zip->getNameIndex($i);
                    $files[] = $fileName;
                }
                $zip->close();

                // Convert file list ke tree structure
                $fileTree = $this->buildFileTree($files);

                return response()->json(['success' => true, 'fileTree' => $fileTree]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal membuka file ZIP']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat membaca ZIP']);
        }
    }

    /**
     * Konversi daftar file ke struktur folder
     */
    private function buildFileTree($files)
    {
        $tree = [];

        foreach ($files as $file) {
            $parts = explode('/', $file);
            $current = &$tree;

            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }

        return $tree;
    }

    public function previewSoal($id)
    {
        try {
            $bankSoal = BankSoal::findOrFail($id);
            $zipPath = storage_path('app/public/' . $bankSoal->file_soal);

            if (!file_exists($zipPath)) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan']);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $extractPath = storage_path("app/public/bank-soal/{$id}");
                if (!file_exists($extractPath)) {
                    mkdir($extractPath, 0777, true);
                }
                $zip->extractTo($extractPath);
                $zip->close();

                // Cari file soal dalam ZIP Blackboard (bisa .dat, .xml)
                $questionFiles = glob("$extractPath/*_questions.dat");

                if (empty($questionFiles)) {
                    return response()->json(['success' => false, 'message' => 'File soal tidak ditemukan dalam ZIP']);
                }

                // Debugging: Log file soal ditemukan
                Log::info('File Soal Ditemukan:', ['file' => $questionFiles[0]]);

                $datContent = file_get_contents($questionFiles[0]);
                Log::info('Isi File Soal:', ['content' => $datContent]);

                // Parsing file XML
                $questions = $this->parseBlackboardXML($datContent, $id);

                // Ambil validasi soal dari database
                $validasi = ValidasiSoal::where('bank_soals_id', $id)->first();
                $soalData = $validasi ? json_decode($validasi->soal, true) : [];

                // Loop untuk menambahkan status validasi ke setiap soal
                foreach ($questions as $index => $question) {
                    $nomorSoal = $index + 1;

                    // Cek apakah soal ini sudah divalidasi
                    $questions[$index]['keterangan_validasi'] = isset($soalData[$nomorSoal]) && is_array($soalData[$nomorSoal])
                        ? (bool) ($soalData[$nomorSoal]['keterangan_validasi'] ?? false)
                        : false;
                }

                return response()->json([
                    'success' => true,
                    'questions' => $questions,
                    'image_path' => asset("storage/bank-soal/{$id}/")
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal membuka file ZIP']);
            }
        } catch (\Exception $e) {
            Log::error('Error saat membaca soal: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat membaca soal']);
        }
    }

    private function parseBlackboardXML($xmlContent, $id)
    {
        try {
            $xml = simplexml_load_string($xmlContent);
            $questions = [];
            $labels = ['A', 'B', 'C', 'D', 'E']; // Label untuk pilihan ganda
            // ✅ Cari folder gambar utama (karena bisa berbeda di setiap soal)
            $baseImagePath = storage_path("app/public/bank-soal/{$id}/");
            $imageFolders = glob("{$baseImagePath}ppg/examview/*", GLOB_ONLYDIR);
            $imageFolderPath = $imageFolders[0] ?? null; // Ambil folder pertama (jika ada)

            if ($imageFolderPath) {
                $imageFolderName = basename($imageFolderPath);
                $publicImagePath = asset("storage/bank-soal/{$id}/ppg/examview/{$imageFolderName}/00001_res/");
            } else {
                $publicImagePath = asset("storage/bank-soal/{$id}/");
            }

            foreach ($xml->assessment->section->item as $item) {
                // **✅ Ambil teks pertanyaan**
                $questionText = "";
                if ($item->xpath('.//mat_formattedtext')) {
                    $questionText = (string) $item->xpath('.//mat_formattedtext')[0];
                }

                // **✅ Ganti path gambar dalam pertanyaan**
                $questionText = preg_replace_callback('/<img .*?src="(.*?)".*?>/i', function ($matches) use ($publicImagePath) {
                    $imageSrc = basename($matches[1]); // Ambil nama file gambar
                    return '<img src="' . $publicImagePath . '/' . $imageSrc . '" class="img-fluid" />';
                }, $questionText);

                // **✅ Ambil opsi jawaban**
                $options = [];
                $optionMapping = [];
                $index = 0;

                $responseLabels = $item->xpath('.//response_label');
                foreach ($responseLabels as $responseLabel) {
                    $ident = (string) $responseLabel['ident'];
                    $answerText = "";

                    if ($responseLabel->xpath('.//mat_formattedtext')) {
                        $answerText = (string) $responseLabel->xpath('.//mat_formattedtext')[0];
                    }

                    // **✅ Ganti path gambar dalam opsi jawaban**
                    $answerText = preg_replace_callback('/<img .*?src="(.*?)".*?>/i', function ($matches) use ($baseImagePath) {
                        $imageSrc = basename($matches[1]); // Ambil nama file gambar
                        return '<img src="' . $baseImagePath . $imageSrc . '" class="img-fluid" />';
                    }, $answerText);

                    $cleanAnswerText = strip_tags(html_entity_decode($answerText), '<img>');

                    // ✅ Format jawaban dengan label A, B, C, D, E
                    $label = $labels[$index] ?? chr(65 + $index);
                    $formattedOption = "$label. " . $cleanAnswerText;

                    $options[] = $formattedOption;
                    $optionMapping[$ident] = $formattedOption; // Simpan ID untuk jawaban benar
                    $index++;
                }

                // **✅ Ambil jawaban benar dari resprocessing**
                $correctAnswer = "";
                $correctAnswerNodes = $item->xpath('.//resprocessing//varequal');
                foreach ($correctAnswerNodes as $correctAnswerNode) {
                    $correctAnswerId = (string) $correctAnswerNode;
                    if (isset($optionMapping[$correctAnswerId])) {
                        $correctAnswer = $optionMapping[$correctAnswerId];
                        break;
                    }
                }

                // **✅ Simpan hasil parsing**
                $questions[] = [
                    'text' => trim(strip_tags(html_entity_decode($questionText), '<img>')), // Bersihkan pertanyaan tapi tetap simpan gambar
                    'options' => $options, // Simpan opsi jawaban
                    'correctAnswer' => trim(strip_tags(html_entity_decode($correctAnswer))), // Simpan jawaban benar
                ];
            }

            // **✅ Debug log untuk melihat hasil parsing**
            Log::info('Soal Berhasil Diparsing:', $questions);
            return $questions;
        } catch (\Exception $e) {
            Log::error('Error parsing Blackboard XML: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fungsi untuk Menghapus Direktori Sementara
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filePath = "$dir/$file";
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }

        rmdir($dir);
    }

    public function simpanValidasiSoal(Request $request)
    {
        $request->validate([
            'bank_soals_id' => 'required|uuid',
            'nomor_soal' => 'required|integer',
            'keterangan_validasi' => 'required|boolean',
        ]);

        $guru_id = Auth::guard('guru')->user()->guru_id; // Ambil ID Guru yang login
        $bank_soals_id = $request->bank_soals_id;
        $nomor_soal = $request->nomor_soal;
        $keterangan_validasi = $request->keterangan_validasi;

        // Cek apakah sudah ada data validasi untuk bank soal ini
        $validasi = ValidasiSoal::where('bank_soals_id', $bank_soals_id)->first();

        if ($validasi) {
            // Decode JSON soal jika berbentuk string, pastikan selalu array
            $soalData = is_string($validasi->soal) ? json_decode($validasi->soal, true) : $validasi->soal;

            if (!is_array($soalData)) {
                $soalData = []; // Jika tidak berbentuk array, set array kosong
            }

            // Update atau tambahkan nomor soal yang tervalidasi
            $soalData[$nomor_soal] = [
                'nomor_soal' => $nomor_soal,
                'keterangan_validasi' => $keterangan_validasi,
            ];

            // Simpan perubahan dengan encoding ke JSON
            $validasi->update(['soal' => json_encode($soalData)]);
        } else {
            // Jika belum ada validasi, buat baru dengan JSON yang benar
            ValidasiSoal::create([
                'guru_id' => $guru_id,
                'bank_soals_id' => $bank_soals_id,
                'soal' => json_encode([
                    $nomor_soal => ['nomor_soal' => $nomor_soal, 'keterangan_validasi' => $keterangan_validasi]
                ]),
                'status' => true
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Validasi soal berhasil disimpan!']);
    }
}
