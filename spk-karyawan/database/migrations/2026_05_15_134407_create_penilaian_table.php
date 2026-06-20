<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            $table->foreignId('periode_kriteria_id')->constrained('periode_kriteria')->cascadeOnDelete();
            $table->foreignId('periode_sub_kriteria_id')->constrained('periode_sub_kriteria')->cascadeOnDelete();

            // Nilai mentah (skor 1–5 dari sub-kriteria yang dipilih)
            $table->integer('nilai');

            // Hasil normalisasi SAW (diisi saat proses hitung)
            $table->decimal('nilai_normalisasi', 10, 6)->nullable();
            $table->decimal('nilai_terbobot', 10, 6)->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['periode_id', 'karyawan_id', 'periode_kriteria_id'], 'uq_penilaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};