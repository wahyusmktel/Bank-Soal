<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ValidasiSoal extends Model
{
    use HasFactory;

    protected $table = 'validasi_soals';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'guru_id', 'bank_soals_id', 'soal', 'status'];

    protected $casts = [
        'soal' => AsArrayObject::class, // Menggunakan JSON cast
        'status' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soals_id');
    }
}
