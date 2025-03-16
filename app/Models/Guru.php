<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'gurus'; // Nama tabel di database

    protected $primaryKey = 'id'; // Primary key menggunakan UUID
    public $incrementing = false; // UUID tidak auto-increment
    protected $keyType = 'string'; // UUID bertipe string

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Nama',
        'NUPTK',
        'JK',
        'Tempat_Lahir',
        'Tanggal_Lahir',
        'NIP',
        'Status_Kepegawaian',
        'Jenis_PTK',
        'Agama',
        'Alamat_Jalan',
        'RT',
        'RW',
        'Nama_Dusun',
        'Desa_Kelurahan',
        'Kecamatan',
        'Kode_Pos',
        'Telepon',
        'HP',
        'Email',
        'password',
        'Tugas_Tambahan',
        'SK_CPNS',
        'Tanggal_CPNS',
        'SK_Pengangkatan',
        'TMT_Pengangkatan',
        'Lembaga_Pengangkatan',
        'Pangkat_Golongan',
        'Sumber_Gaji',
        'Nama_Ibu_Kandung',
        'Status_Perkawinan',
        'Nama_Suami_Istri',
        'NIP_Suami_Istri',
        'Pekerjaan_Suami_Istri',
        'TMT_PNS',
        'Sudah_Lisensi_Kepala_Sekolah',
        'Pernah_Diklat_Kepengawasan',
        'Keahlian_Braille',
        'Keahlian_Bahasa_Isyarat',
        'NPWP',
        'Nama_Wajib_Pajak',
        'Kewarganegaraan',
        'Bank',
        'Nomor_Rekening_Bank',
        'Rekening_Atas_Nama',
        'NIK',
        'No_KK',
        'Karpeg',
        'Karis_Karsu',
        'Lintang',
        'Bujur',
        'NUKS',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Tanggal_Lahir' => 'date',
        'Tanggal_CPNS' => 'date',
        'TMT_Pengangkatan' => 'date',
        'TMT_PNS' => 'date',
        'Sudah_Lisensi_Kepala_Sekolah' => 'boolean',
        'Pernah_Diklat_Kepengawasan' => 'boolean',
        'Keahlian_Braille' => 'boolean',
        'Keahlian_Bahasa_Isyarat' => 'boolean',
        'status' => 'boolean',
    ];

    public function akunGuru(): HasOne
    {
        return $this->hasOne(AkunGuru::class, 'guru_id');
    }
}
