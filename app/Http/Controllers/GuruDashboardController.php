<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MapingMapel;
use App\Models\DataUjian;
use App\Models\TahunPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Support\Facades\Log;

class GuruDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil ID Guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Ambil data inputan pencarian (filter)
            $filterDataUjian = $request->input('data_ujian_id');
            $filterTahunPelajaran = $request->input('tahun_pelajaran_id');

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
                ]);

            // Filter berdasarkan Data Ujian
            if (!empty($filterDataUjian)) {
                $mapingMapels->where('data_ujian_id', $filterDataUjian);
            }

            // Filter berdasarkan Tahun Pelajaran
            if (!empty($filterTahunPelajaran)) {
                $mapingMapels->whereHas('dataUjian', function ($query) use ($filterTahunPelajaran) {
                    $query->where('tahun_pelajaran_id', $filterTahunPelajaran);
                });
            }

            // Paginasi (10 data per halaman)
            $mapingMapels = $mapingMapels->paginate(10);

            // **Optimalkan data sebelum dikirim ke view**
            $mapingMapels->getCollection()->transform(function ($maping) {
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
                        'mapel' => $mapels[$data['mata_pelajaran_id']] ?? 'Unknown Mapel',
                        'kelas' => $kelas[$data['kelas_id']] ?? 'Unknown Kelas',
                    ];
                });

                return $maping;
            });

            // Ambil daftar ujian & tahun pelajaran untuk filter
            $dataUjians = DataUjian::where('status', true)->orderBy('nama_ujian', 'asc')->get();
            $tahunPelajarans = TahunPelajaran::orderBy('nama_tahun', 'desc')->get();

            // Kirim data ke view
            return view('guru.dashboard', compact('mapingMapels', 'dataUjians', 'tahunPelajarans', 'filterDataUjian', 'filterTahunPelajaran'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Error saat mengambil data mapping untuk guru: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
}
