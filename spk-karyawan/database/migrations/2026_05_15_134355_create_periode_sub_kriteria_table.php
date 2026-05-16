<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Snapshot sub-kriteria (skala nilai) per periode.
     * Sama seperti periode_kriteria, ini memastikan skala penilaian
     * yang dipakai untuk periode lama tidak berubah jika admin
     * mengubah sub-kriteria master di kemudian hari.
     */
    public function up(): void
    {
        Schema::create('periode_sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->foreignId('periode_kriteria_id')->constrained('periode_kriteria')->cascadeOnDelete();
            $table->foreignId('sub_kriteria_id')->nullable()->constrained('sub_kriteria')->nullOnDelete();

            // Snapshot data sub-kriteria
            $table->string('label', 100);
            $table->integer('skor');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_sub_kriteria');
    }
};