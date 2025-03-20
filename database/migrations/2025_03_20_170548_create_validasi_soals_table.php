<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('validasi_soals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guru_id');
            $table->uuid('bank_soals_id');
            $table->json('soal'); // Menyimpan data validasi dalam JSON
            $table->boolean('status')->default(true); // Status otomatis bernilai true saat insert
            $table->timestamps();

            // Foreign key ke bank_soals agar jika bank_soals dihapus, validasi ikut terhapus
            $table->foreign('bank_soals_id')->references('id')->on('bank_soals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('validasi_soals');
    }
};
