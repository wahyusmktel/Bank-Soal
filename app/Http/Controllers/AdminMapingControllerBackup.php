<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapingMapel;
use App\Models\Guru;
use App\Models\DataUjian;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminMapingController extends Controller
{
    public function index()
    {
        $mapings = MapingMapel::with('guru', 'dataUjian.tahunPelajaran')->get();
        $gurus = Guru::all();
        $dataUjians = DataUjian::with('tahunPelajaran')->get();
        $mataPelajarans = MataPelajaran::all();
        $kelas = Kelas::all();

        return view('admin.MapingMapel.index', compact('mapings', 'gurus', 'dataUjians', 'mataPelajarans', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'data_ujian_id' => 'required|exists:data_ujians,id',
            'mata_pelajaran' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Format yang akan disimpan: [{ "mata_pelajaran_id": "1", "kelas_id": ["2", "3"] }, ...]
            $mapingData = [];

            foreach ($request->mata_pelajaran as $mapel) {
                $mapingData[] = [
                    'mata_pelajaran_id' => $mapel['mata_pelajaran_id'], // Simpan ID Mata Pelajaran
                    'kelas_id' => $mapel['kelas_id'] // Simpan Array ID Kelas
                ];
            }

            $maping = new MapingMapel();
            $maping->id = Str::uuid();
            $maping->guru_id = $request->guru_id;
            $maping->data_ujian_id = $request->data_ujian_id;
            $maping->mata_pelajaran_id = json_encode($mapingData); // Simpan dalam JSON
            $maping->status = true;
            $maping->save();

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'data_ujian_id' => 'required|exists:data_ujians,id',
            'mata_pelajaran' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $maping = MapingMapel::findOrFail($id);
            $maping->guru_id = $request->guru_id;
            $maping->data_ujian_id = $request->data_ujian_id;
            $maping->mata_pelajaran_id = json_encode($request->mata_pelajaran);
            $maping->save();

            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            MapingMapel::destroy($id);
            return redirect()->back()->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
