<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('data_ujians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahun_pelajaran_id');
            $table->string('nama_ujian');
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_akhir');
            $table->boolean('status')->default(false);
            $table->timestamps();

            // Foreign Key ke tahun_pelajarans
            $table->foreign('tahun_pelajaran_id')->references('id')->on('tahun_pelajarans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_ujians');
    }
};
