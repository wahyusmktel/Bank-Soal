<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('Nama')->nullable();
            $table->string('NUPTK')->nullable();
            $table->enum('JK', ['L', 'P'])->nullable(); // L = Laki-laki, P = Perempuan
            $table->string('Tempat_Lahir')->nullable();
            $table->date('Tanggal_Lahir')->nullable();
            $table->string('NIP')->nullable();
            $table->string('Status_Kepegawaian')->nullable();
            $table->string('Jenis_PTK')->nullable();
            $table->string('Agama')->nullable();
            $table->string('Alamat_Jalan')->nullable();
            $table->string('RT')->nullable();
            $table->string('RW')->nullable();
            $table->string('Nama_Dusun')->nullable();
            $table->string('Desa_Kelurahan')->nullable();
            $table->string('Kecamatan')->nullable();
            $table->string('Kode_Pos')->nullable();
            $table->string('Telepon')->nullable();
            $table->string('HP')->nullable();
            $table->string('Email')->nullable();
            $table->string('Tugas_Tambahan')->nullable();
            $table->string('SK_CPNS')->nullable();
            $table->date('Tanggal_CPNS')->nullable();
            $table->string('SK_Pengangkatan')->nullable();
            $table->date('TMT_Pengangkatan')->nullable();
            $table->string('Lembaga_Pengangkatan')->nullable();
            $table->string('Pangkat_Golongan')->nullable();
            $table->string('Sumber_Gaji')->nullable();
            $table->string('Nama_Ibu_Kandung')->nullable();
            $table->string('Status_Perkawinan')->nullable();
            $table->string('Nama_Suami_Istri')->nullable();
            $table->string('NIP_Suami_Istri')->nullable();
            $table->string('Pekerjaan_Suami_Istri')->nullable();
            $table->date('TMT_PNS')->nullable();
            $table->boolean('Sudah_Lisensi_Kepala_Sekolah')->nullable();
            $table->boolean('Pernah_Diklat_Kepengawasan')->nullable();
            $table->boolean('Keahlian_Braille')->nullable();
            $table->boolean('Keahlian_Bahasa_Isyarat')->nullable();
            $table->string('NPWP')->nullable();
            $table->string('Nama_Wajib_Pajak')->nullable();
            $table->string('Kewarganegaraan')->nullable();
            $table->string('Bank')->nullable();
            $table->string('Nomor_Rekening_Bank')->nullable();
            $table->string('Rekening_Atas_Nama')->nullable();
            $table->string('NIK'); // Tidak nullable
            $table->string('No_KK')->nullable();
            $table->string('Karpeg')->nullable();
            $table->string('Karis_Karsu')->nullable();
            $table->string('Lintang')->nullable();
            $table->string('Bujur')->nullable();
            $table->string('NUKS')->nullable();
            $table->boolean('status')->default(true); // Default true
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
