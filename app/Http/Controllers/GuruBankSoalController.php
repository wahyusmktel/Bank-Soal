<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BankSoal;
use App\Models\DataUjian;
use App\Models\Kelas;
use App\Models\MapingMapel;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Log;

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
                ->with([
                    'dataUjian' => function ($query) {
                        $query->where('status', true);
                    },
                    'dataUjian.tahunPelajaran'
                ])
                ->get(); // Gunakan get() karena tidak melakukan pagination di sini

            // Optimalkan data sebelum dikirim ke view
            $mapingMapels = $mapingMapels->map(function ($maping) {
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true);
                if (!is_array($mapelKelasData)) {
                    $mapelKelasData = [];
                }

                $mapelIds = collect($mapelKelasData)->pluck('mata_pelajaran_id')->toArray();
                $kelasIds = collect($mapelKelasData)->pluck('kelas_id')->toArray();

                // Ambil data mapel & kelas hanya sekali untuk mengurangi query
                $mapels = MataPelajaran::whereIn('id', $mapelIds)->pluck('nama_mapel', 'id')->toArray();
                $kelas = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas', 'id')->toArray();

                // Simpan hasil dalam bentuk array agar lebih mudah diakses di view
                $maping->mapel_kelas_list = collect($mapelKelasData)->map(function ($data) use ($mapels, $kelas) {
                    return [
                        'mata_pelajaran_id' => $data['mata_pelajaran_id'],
                        'mapel' => $mapels[$data['mata_pelajaran_id']] ?? 'Unknown Mapel',
                        'kelas_id' => $data['kelas_id'],
                        'kelas' => $kelas[$data['kelas_id']] ?? 'Unknown Kelas',
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
            $request->validate([
                'mata_pelajaran_id' => 'required|uuid|exists:mata_pelajarans,id',
                'kelas_id' => 'required|array|min:1',
                'kelas_id.*' => 'uuid|exists:kelas,id',
                'file_soal' => 'required|file|mimes:zip|max:5120', // Batas ukuran 5MB
            ]);

            // Ambil ID guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Ambil data mapel dan kelas
            $mapelId = $request->mata_pelajaran_id;
            $kelasIds = $request->kelas_id;

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

            // Format nama file: "Nama_Ujian_Nama_Tahun_Semester_Nama_Mapel_Nama_Kelas.zip"
            $fileName = "{$dataUjian->nama_ujian}_{$tahunPelajaran}_Semester_{$semester}_{$mapelNama}_{$kelasNama}.zip";

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
}
