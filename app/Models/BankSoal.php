<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BankSoal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bank_soals';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'guru_id',
        'mata_pelajaran_id',
        'file_soal',
        'status',
    ];

    protected $casts = [
        'mata_pelajaran_id' => 'array', // Konversi JSON ke array otomatis
        'status' => 'boolean',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
