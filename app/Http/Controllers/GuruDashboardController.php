<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
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

            // Ambil semua bank soal milik guru yang sedang login dan ujian aktif
            $bankSoalGuru = BankSoal::where('guru_id', $guruId)
                ->whereIn('data_ujian_id', $mapingMapels->pluck('data_ujian_id')->unique())
                ->get();

            // Optimasi data sebelum dikirim ke view
            $mapingMapels->getCollection()->transform(function ($maping) use ($bankSoalGuru) {
                $mapelKelasData = json_decode($maping->mata_pelajaran_id, true) ?? [];

                $mapelIds = collect($mapelKelasData)->pluck('mata_pelajaran_id')->unique()->toArray();
                $kelasIds = collect($mapelKelasData)->pluck('kelas_id')->flatten()->unique()->toArray();

                $mapels = MataPelajaran::whereIn('id', $mapelIds)->pluck('nama_mapel', 'id')->toArray();
                $kelas = Kelas::whereIn('id', $kelasIds)->pluck('nama_kelas', 'id')->toArray();

                $maping->mapel_kelas_list = collect($mapelKelasData)->map(function ($data) use ($maping, $mapels, $kelas, $bankSoalGuru) {
                    $kelasIds = $data['kelas_id'];
                    $mapelId = $data['mata_pelajaran_id'];

                    // Filter hanya bank soal untuk guru & ujian ini
                    $bankSoals = $bankSoalGuru->where('data_ujian_id', $maping->data_ujian_id);

                    // Cek apakah ada bank soal yang memiliki mapel dan seluruh kelas
                    $sudahUpload = $bankSoals->contains(function ($soal) use ($mapelId, $kelasIds) {
                        $decoded = json_decode($soal->getRawOriginal('mata_pelajaran_id'), true);

                        if (!is_array($decoded)) return false;

                        return (
                            ($decoded['mata_pelajaran_id'] ?? null) === $mapelId &&
                            empty(array_diff($kelasIds, $decoded['kelas_id'] ?? []))
                        );
                    });

                    return [
                        'mapel' => $mapels[$mapelId] ?? 'Unknown Mapel',
                        'kelas' => collect($kelasIds)->map(fn($k) => $kelas[$k] ?? 'Unknown Kelas')->implode(', '),
                        'sudah_upload' => $sudahUpload,
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
