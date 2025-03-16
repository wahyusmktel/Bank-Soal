<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class GuruImport implements ToModel, WithHeadingRow
{
    public $addedCount = 0;
    public $updatedCount = 0;
    
    public function model(array $row)
    {
        // Pastikan nama kolom bersih tanpa spasi tambahan
        $row = array_change_key_case(array_map('trim', $row), CASE_LOWER);

        // Pastikan NIK tidak kosong
        if (!isset($row['nik']) || empty($row['nik'])) {
            Log::warning('Data dilewati karena NIK kosong: ', $row);
            return null; // Lewati data jika NIK tidak ada
        }

        // Cek apakah data dengan NIK sudah ada
        $guru = Guru::where('NIK', $row['nik'])->first();

        // Jika data sudah ada, lakukan update
        if ($guru) {
            $guru->update([
                'Nama'=> $row['nama'] ?? null,
                'NUPTK'=> $row['nuptk'] ?? null,
                'JK'=> $row['jk'] ?? null,
                'Tempat_Lahir'=> $row['tempat_lahir'] ?? null,
                'Tanggal_Lahir' => $this->convertToDate($row['tanggal_lahir'] ?? null),
                'NIP'=> $row['nip'] ?? null,
                'Status_Kepegawaian'=> $row['status_kepegawaian'] ?? null,
                'Jenis_PTK'=> $row['jenis_ptk'] ?? null,
                'Agama'=> $row['agama'] ?? null,
                'Alamat_Jalan'=> $row['alamat_jalan'] ?? null,
                'RT'=> $row['rt'] ?? null,
                'RW'=> $row['rw'] ?? null,
                'Nama_Dusun'=> $row['nama_dusun'] ?? null,
                'Desa_Kelurahan'=> $row['desa_kelurahan'] ?? null,
                'Kecamatan'=> $row['kecamatan'] ?? null,
                'Kode_Pos'=> $row['kode_pos'] ?? null,
                'Telepon'=> $row['telepon'] ?? null,
                'HP'=> $row['hp'] ?? null,
                'Email'=> $row['email'] ?? null,
                'Tugas_Tambahan'=> $row['tugas_tambahan'] ?? null,
                'SK_CPNS'=> $row['sk_cpns'] ?? null,
                'Tanggal_CPNS' => $this->convertToDate($row['tanggal_cpns'] ?? null),
                'SK_Pengangkatan'=> $row['sk_pengangkatan'] ?? null,
                'TMT_Pengangkatan' => $this->convertToDate($row['tmt_pengangkatan'] ?? null),
                'Lembaga_Pengangkatan'=> $row['lembaga_pengangkatan'] ?? null,
                'Pangkat_Golongan'=> $row['pangkat_golongan'] ?? null,
                'Sumber_Gaji'=> $row['sumber_gaji'] ?? null,
                'Nama_Ibu_Kandung'=> $row['nama_ibu_kandung'] ?? null,
                'Status_Perkawinan'=> $row['status_perkawinan'] ?? null,
                'Nama_Suami_Istri'=> $row['nama_suami_istri'] ?? null,
                'NIP_Suami_Istri'=> $row['nip_suami_istri'] ?? null,
                'Pekerjaan_Suami_Istri'=> $row['pekerjaan_suami_istri'] ?? null,
                'TMT_PNS' => $this->convertToDate($row['tmt_pns'] ?? null),
                'Sudah_Lisensi_Kepala_Sekolah' => $this->convertToBoolean($row['sudah_lisensi_kepala_sekolah'] ?? null),
                'Pernah_Diklat_Kepengawasan' => $this->convertToBoolean($row['pernah_diklat_kepengawasan'] ?? null),
                'Keahlian_Braille' => $this->convertToBoolean($row['keahlian_braille'] ?? null),
                'Keahlian_Bahasa_Isyarat' => $this->convertToBoolean($row['keahlian_bahasa_isyarat'] ?? null),
                'NPWP'=> $row['npwp'] ?? null,
                'Nama_Wajib_Pajak'=> $row['nama_wajib_pajak'] ?? null,
                'Kewarganegaraan'=> $row['kewarganegaraan'] ?? null,
                'Bank'=> $row['bank'] ?? null,
                'Nomor_Rekening_Bank'=> $row['nomor_rekening_bank'] ?? null,
                'Rekening_Atas_Nama'=> $row['rekening_atas_nama'] ?? null,
                'NIK'=> $row['nik'],
                'No_KK'=> $row['no_kk'] ?? null,
                'Karpeg'=> $row['karpeg'] ?? null,
                'Karis_Karsu'=> $row['karis_karsu'] ?? null,
                'Lintang'=> $row['lintang'] ?? null,
                'Bujur'=> $row['bujur'] ?? null,
                'NUKS'=> $row['nuks'] ?? null,
                'status' => true,
            ]);

            Log::info("Data diperbarui untuk NIK: " . $row['nik']);
            $this->updatedCount++; // Tambah counter update
            return null; // Return null agar tidak menambahkan data baru
        }

        // Jika data belum ada, lakukan insert data baru
        $this->addedCount++; // Tambah counter baru
        return new Guru([
            'id' => Str::uuid(),
            'Nama'=> $row['nama'] ?? null,
            'NUPTK'=> $row['nuptk'] ?? null,
            'JK'=> $row['jk'] ?? null,
            'Tempat_Lahir'=> $row['tempat_lahir'] ?? null,
            'Tanggal_Lahir' => $this->convertToDate($row['tanggal_lahir'] ?? null),
            'NIP'=> $row['nip'] ?? null,
            'Status_Kepegawaian'=> $row['status_kepegawaian'] ?? null,
            'Jenis_PTK'=> $row['jenis_ptk'] ?? null,
            'Agama'=> $row['agama'] ?? null,
            'Alamat_Jalan'=> $row['alamat_jalan'] ?? null,
            'RT'=> $row['rt'] ?? null,
            'RW'=> $row['rw'] ?? null,
            'Nama_Dusun'=> $row['nama_dusun'] ?? null,
            'Desa_Kelurahan'=> $row['desa_kelurahan'] ?? null,
            'Kecamatan'=> $row['kecamatan'] ?? null,
            'Kode_Pos'=> $row['kode_pos'] ?? null,
            'Telepon'=> $row['telepon'] ?? null,
            'HP'=> $row['hp'] ?? null,
            'Email'=> $row['email'] ?? null,
            'Tugas_Tambahan'=> $row['tugas_tambahan'] ?? null,
            'SK_CPNS'=> $row['sk_cpns'] ?? null,
            'Tanggal_CPNS' => $this->convertToDate($row['tanggal_cpns'] ?? null),
            'SK_Pengangkatan'=> $row['sk_pengangkatan'] ?? null,
            'TMT_Pengangkatan' => $this->convertToDate($row['tmt_pengangkatan'] ?? null),
            'Lembaga_Pengangkatan'=> $row['lembaga_pengangkatan'] ?? null,
            'Pangkat_Golongan'=> $row['pangkat_golongan'] ?? null,
            'Sumber_Gaji'=> $row['sumber_gaji'] ?? null,
            'Nama_Ibu_Kandung'=> $row['nama_ibu_kandung'] ?? null,
            'Status_Perkawinan'=> $row['status_perkawinan'] ?? null,
            'Nama_Suami_Istri'=> $row['nama_suami_istri'] ?? null,
            'NIP_Suami_Istri'=> $row['nip_suami_istri'] ?? null,
            'Pekerjaan_Suami_Istri'=> $row['pekerjaan_suami_istri'] ?? null,
            'TMT_PNS' => $this->convertToDate($row['tmt_pns'] ?? null),
            'Sudah_Lisensi_Kepala_Sekolah' => $this->convertToBoolean($row['sudah_lisensi_kepala_sekolah'] ?? null),
            'Pernah_Diklat_Kepengawasan' => $this->convertToBoolean($row['pernah_diklat_kepengawasan'] ?? null),
            'Keahlian_Braille' => $this->convertToBoolean($row['keahlian_braille'] ?? null),
            'Keahlian_Bahasa_Isyarat' => $this->convertToBoolean($row['keahlian_bahasa_isyarat'] ?? null),
            'NPWP'=> $row['npwp'] ?? null,
            'Nama_Wajib_Pajak'=> $row['nama_wajib_pajak'] ?? null,
            'Kewarganegaraan'=> $row['kewarganegaraan'] ?? null,
            'Bank'=> $row['bank'] ?? null,
            'Nomor_Rekening_Bank'=> $row['nomor_rekening_bank'] ?? null,
            'Rekening_Atas_Nama'=> $row['rekening_atas_nama'] ?? null,
            'NIK'=> $row['nik'],
            'No_KK'=> $row['no_kk'] ?? null,
            'Karpeg'=> $row['karpeg'] ?? null,
            'Karis_Karsu'=> $row['karis_karsu'] ?? null,
            'Lintang'=> $row['lintang'] ?? null,
            'Bujur'=> $row['bujur'] ?? null,
            'NUKS'=> $row['nuks'] ?? null,
            'status' => true,
        ]);
    }

    /**
     * Konversi nilai dari "Ya"/"Tidak" menjadi boolean.
     */
    private function convertToBoolean($value)
    {
        return strtolower(trim($value)) === 'ya';
    }

    private function convertToDate($value)
    {
        // Jika kosong atau tidak valid, kembalikan null
        if (empty($value) || $value == '0000-00-00') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Kesalahan format tanggal: {$value}");
            return null;
        }
    }

    /**
     * Mendapatkan jumlah data yang ditambahkan.
     */
    public function getAddedCount()
    {
        return $this->addedCount;
    }

    /**
     * Mendapatkan jumlah data yang diperbarui.
     */
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }

    /**
     * Menggunakan chunk untuk import lebih cepat.
     */
    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
