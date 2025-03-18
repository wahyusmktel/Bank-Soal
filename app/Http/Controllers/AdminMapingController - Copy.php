<?php

namespace App\Http\Controllers;

use App\Models\MapingMapel;
use App\Models\Guru;
use App\Models\DataUjian;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Kelas;
use App\Models\TahunPelajaran;

class AdminMapingController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data inputan pencarian
            $search = $request->input('search');
            $filterDataUjian = $request->input('data_ujian_id');
            $filterTahunPelajaran = $request->input('tahun_pelajaran_id');

            // Query data mapping dengan relasi
            $mapingMapels = MapingMapel::with(['guru', 'dataUjian.tahunPelajaran']);

            // Filter berdasarkan nama guru
            if (!empty($search)) {
                $mapingMapels->whereHas('guru', function ($query) use ($search) {
                    $query->where('Nama', 'LIKE', "%$search%");
                });
            }

            // Filter berdasarkan Data Ujian
            if (!empty($filterDataUjian)) {
                $mapingMapels->where('data_ujian_id', $filterDataUjian);
            }

            // Filter berdasarkan Tahun Pelajaran (nama_tahun - semester)
            if (!empty($filterTahunPelajaran)) {
                $mapingMapels->whereHas('dataUjian', function ($query) use ($filterTahunPelajaran) {
                    $query->where('tahun_pelajaran_id', $filterTahunPelajaran);
                });
            }

            // Paginasi (10 data per halaman)
            $mapingMapels = $mapingMapels->paginate(10);

            // Proses manipulasi data sebelum dikirim ke view
            $mapingMapels->getCollection()->transform(function ($maping) {
                // Decode JSON dari kolom `mata_pelajaran_id`
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true);

                // Pastikan data berbentuk array
                if (!is_array($mapelKelasData)) {
                    $mapelKelasData = [];
                }

                // Ambil ID mata pelajaran dan kelas
                $mapelIds = collect($mapelKelasData)->pluck('mata_pelajaran_id')->toArray();
                $kelasIds = collect($mapelKelasData)->pluck('kelas_id')->flatten()->toArray();

                // Ambil data nama mapel dan kelas dari database
                $mapels = MataPelajaran::whereIn('id', $mapelIds)->pluck('nama_mapel', 'id')->toArray();
                $kelas = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas', 'id')->toArray();

                // Gabungkan Mapel & Kelas dalam satu format "1. Matematika - XII RPL 1"
                $mapelKelasList = [];
                foreach ($mapelKelasData as $index => $data) {
                    $mapelNama = $mapels[$data['mata_pelajaran_id']] ?? 'Unknown Mapel';

                    // Jika `kelas_id` adalah array, kita harus mengonversinya ke string daftar nama kelas
                    $kelasIds = is_array($data['kelas_id']) ? $data['kelas_id'] : [$data['kelas_id']];
                    $kelasNamaList = collect($kelasIds)->map(fn($id) => $kelas[$id] ?? 'Unknown Kelas')->toArray();

                    // Gabungkan semua nama kelas dengan koma
                    $kelasNama = implode(', ', $kelasNamaList);

                    // Format daftar mapel dan kelas
                    $mapelKelasList[] = ($index + 1) . ". $mapelNama - $kelasNama";
                }

                // Simpan hasil dalam properti baru agar bisa diakses di Blade dalam format list HTML
                // $maping->mapel_kelas_nama = !empty($mapelKelasList) ? '<ul><li>' . implode('</li><li>', $mapelKelasList) . '</li></ul>' : '-';
                $maping->mapel_kelas_nama = !empty($mapelKelasList)
                    ? implode('<br>', $mapelKelasList)
                    : '-';

                // **Tambahkan jumlah mata pelajaran**
                $maping->jumlah_mapel = count($mapelKelasData);

                return $maping;
            });

            // Ambil daftar guru, ujian, mata pelajaran & kelas
            $gurus = Guru::orderBy('Nama', 'asc')->get();
            $dataUjians = DataUjian::orderBy('nama_ujian', 'asc')->get();
            $mataPelajarans = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
            $kelasuuu = Kelas::orderBy('tingkat_kelas', 'asc')->get();
            $tahunPelajarans = TahunPelajaran::orderBy('nama_tahun', 'desc')->get();
            $semesters = TahunPelajaran::distinct()->pluck('semester');

            // Kirim data ke view
            return view('admin.MapingMapel.index', compact('mapingMapels', 'search', 'gurus', 'dataUjians', 'mataPelajarans', 'kelasuuu', 'tahunPelajarans', 'semesters', 'filterDataUjian', 'filterTahunPelajaran'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data mapping: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Data yang dikirim oleh user:', $request->all());

            // Validasi input
            $request->validate([
                'guru_id' => 'required|uuid|exists:gurus,id',
                'data_ujian_id' => 'required|uuid|exists:data_ujians,id',
                'mata_pelajaran_id' => 'required|array|min:1',
                'mata_pelajaran_id.*' => 'uuid|exists:mata_pelajarans,id',
                'kelas_id' => 'required|array|min:1',
                'kelas_id.*' => 'array|min:1', // Kelas harus berupa array
                'kelas_id.*.*' => 'uuid|exists:kelas,id', // Setiap item dalam array harus UUID
            ]);

            Log::info('Data setelah validasi:', [
                'guru_id' => $request->guru_id,
                'data_ujian_id' => $request->data_ujian_id,
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'kelas_id' => $request->kelas_id,
            ]);

            // **Format data JSON (Mapel + Kelas)**
            $mapelKelasData = [];
            foreach ($request->mata_pelajaran_id as $index => $mapelId) {
                $kelasIds = isset($request->kelas_id[$index]) ? $request->kelas_id[$index] : [];

                $mapelKelasData[] = [
                    'mata_pelajaran_id' => $mapelId,
                    'kelas_id' => $kelasIds, // Menyimpan array kelas dalam JSON
                ];
            }

            Log::info('Format JSON sebelum disimpan:', ['mapel_kelas_data' => $mapelKelasData]);

            // **Simpan data ke database**
            $maping = MapingMapel::create([
                'id' => Str::uuid(),
                'guru_id' => $request->guru_id,
                'data_ujian_id' => $request->data_ujian_id,
                'mata_pelajaran_id' => json_encode($mapelKelasData), // Simpan sebagai JSON
                'status' => true,
            ]);

            Log::info('Mapping berhasil disimpan:', ['mapping_id' => $maping->id]);

            return redirect()->route('admin.maping.index')->with('success', 'Mapping Mata Pelajaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan Mapping Mata Pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Cari data berdasarkan ID
            $maping = MapingMapel::findOrFail($id);

            // Validasi input
            $request->validate([
                'guru_id' => 'required|uuid|exists:gurus,id',
                'data_ujian_id' => 'required|uuid|exists:data_ujians,id',
                'mata_pelajaran_id' => 'required|array|min:1',
                'mata_pelajaran_id.*' => 'uuid|exists:mata_pelajarans,id',
                'kelas_id' => 'required|array|min:1',
                'kelas_id.*' => 'array|min:1',
                'kelas_id.*.*' => 'uuid|exists:kelas,id',
                'status' => 'required|boolean',
            ]);

            // **Gabungkan kelas ke dalam satu array per mata pelajaran**
            $mapelKelasData = [];

            foreach ($request->mata_pelajaran_id as $mapelId) {
                if (isset($request->kelas_id[$mapelId])) {
                    // Jika mata pelajaran sudah ada, tambahkan kelas baru ke array yang sudah ada
                    if (isset($mapelKelasData[$mapelId])) {
                        $mapelKelasData[$mapelId]['kelas_id'] = array_unique(array_merge(
                            $mapelKelasData[$mapelId]['kelas_id'],
                            $request->kelas_id[$mapelId]
                        ));
                    } else {
                        // Jika belum ada, buat entri baru
                        $mapelKelasData[$mapelId] = [
                            'mata_pelajaran_id' => $mapelId,
                            'kelas_id' => $request->kelas_id[$mapelId]
                        ];
                    }
                }
            }

            // Konversi ke format JSON yang benar
            $mapelKelasFormatted = array_values($mapelKelasData);

            // **Update data mapping**
            $maping->update([
                'guru_id' => $request->guru_id,
                'data_ujian_id' => $request->data_ujian_id,
                'mata_pelajaran_id' => json_encode($mapelKelasFormatted),
                'status' => $request->status,
            ]);

            return redirect()->route('admin.maping.index')->with('success', 'Mapping Mata Pelajaran berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui Mapping Mata Pelajaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        try {
            $maping = MapingMapel::findOrFail($id);
            $maping->delete();

            return back()->with('success', 'Mapping berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus mapping: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
