<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->integer('skor');
            $table->decimal('nilai_min', 8, 2)->nullable(); // untuk kriteria berbasis angka (Masa Kerja)
            $table->decimal('nilai_max', 8, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kriteria');
    }
};