<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);          // "Januari 2025"
            $table->tinyInteger('bulan');         // 1–12
            $table->year('tahun');
            // draft = belum input penilaian
            // aktif  = sedang berjalan
            // selesai = sudah dihitung & dikunci (READ-ONLY)
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};