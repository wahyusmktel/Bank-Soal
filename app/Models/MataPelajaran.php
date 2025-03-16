<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MataPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mata_pelajarans';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_mapel',
        'is_produktif',
        'status'
    ];

    protected $casts = [
        'is_produktif' => 'boolean',
        'status' => 'boolean',
    ];
}
