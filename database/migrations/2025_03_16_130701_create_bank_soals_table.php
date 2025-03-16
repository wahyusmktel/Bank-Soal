<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_soals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guru_id');
            $table->json('mata_pelajaran_id'); // Menyimpan ID mata pelajaran dalam bentuk JSON
            $table->string('file_soal'); // Path atau nama file soal
            $table->boolean('status')->default(true); // Default true saat insert data
            $table->timestamps();

            // Foreign key
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soals');
    }
};
