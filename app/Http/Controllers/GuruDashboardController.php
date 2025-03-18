<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\MapingMapel;
use App\Models\DataUjian;
use App\Models\TahunPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class GuruDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil ID Guru yang sedang login
            $guruId = Auth::guard('guru')->user()->guru_id;

            // Ambil data filter dari request
            $filterDataUjian = $request->input('data_ujian_id');
            $filterTahunPelajaran = $request->input('tahun_pelajaran_id');

            // Query data mapping berdasarkan guru yang sedang login & ujian yang aktif
            $mapingMapels = MapingMapel::where('guru_id', $guruId)
                ->whereHas('dataUjian', function ($query) {
                    $query->where('status', true); // Hanya ambil ujian yang aktif
                })
                ->with(['dataUjian.tahunPelajaran']);

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

            // Optimasi data sebelum dikirim ke view
            $mapingMapels->getCollection()->transform(function ($maping) {
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true);
                if (!is_array($mapelKelasData)) {
                    $mapelKelasData = [];
                }

                // Ambil semua ID mapel dan kelas
                $mapelIds = collect($mapelKelasData)->pluck('mata_pelajaran_id')->unique()->toArray();
                $kelasIds = collect($mapelKelasData)->pluck('kelas_id')->flatten()->unique()->toArray();

                // Query untuk menghindari N+1
                $mapels = MataPelajaran::whereIn('id', $mapelIds)->pluck('nama_mapel', 'id')->toArray();
                $kelas = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas', 'id')->toArray();

                // Proses data untuk tampilan
                $maping->mapel_kelas_list = collect($mapelKelasData)->map(function ($data) use ($mapels, $kelas) {
                    return [
                        'mapel' => $mapels[$data['mata_pelajaran_id']] ?? 'Unknown Mapel',
                        'kelas' => collect($data['kelas_id'])->map(fn($k) => $kelas[$k] ?? 'Unknown Kelas')->implode(', '),
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
            Log::error('Error saat mengambil data mapping untuk guru: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }
}
