<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('maping_mapels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guru_id');
            $table->uuid('data_ujian_id');
            $table->json('mata_pelajaran_id'); // Menyimpan JSON dari ID mata pelajaran yang diajar oleh guru
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Foreign Key
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
            $table->foreign('data_ujian_id')->references('id')->on('data_ujians')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maping_mapels');
    }
};
