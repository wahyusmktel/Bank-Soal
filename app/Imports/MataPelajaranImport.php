<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataPelajaranImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return MataPelajaran::updateOrCreate(
            ['nama_mapel' => $row['nama_mapel'] ?? null], // Unik berdasarkan nama_mapel
            [
                'id' => Str::uuid(),
                'is_produktif' => isset($row['is_produktif']) ? ($row['is_produktif'] === 'Ya' ? true : false) : null,
                'status' => true
            ]
        );
    }
}
