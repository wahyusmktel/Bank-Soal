<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MapingMapel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'maping_mapels';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guru_id',
        'data_ujian_id',
        'mata_pelajaran_id',
        'status'
    ];

    protected $casts = [
        'mata_pelajaran_id' => 'array', // Konversi otomatis JSON ke array
        'status' => 'boolean',
    ];

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    // Relasi ke Data Ujian
    public function dataUjian()
    {
        return $this->belongsTo(DataUjian::class, 'data_ujian_id');
    }

    public function tahunPelajaran()
    {
        return $this->hasOneThrough(TahunPelajaran::class, DataUjian::class, 'id', 'id', 'data_ujian_id', 'tahun_pelajaran_id');
    }
}
