<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DataUjian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_ujians';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tahun_pelajaran_id',
        'nama_ujian',
        'tgl_mulai',
        'tgl_akhir',
        'status'
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_akhir' => 'datetime',
        'status' => 'boolean',
    ];

    // Relasi ke Tahun Pelajaran
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
