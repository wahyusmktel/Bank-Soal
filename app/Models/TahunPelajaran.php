<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TahunPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tahun_pelajarans';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_tahun',
        'semester',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function dataUjians()
    {
        return $this->hasMany(DataUjian::class, 'tahun_pelajaran_id');
    }
}
