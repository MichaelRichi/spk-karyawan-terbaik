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
            $table->string('nama', 100);  // contoh: ">= 26 hari", "Sangat Bagus"
            $table->integer('skor');      // nilai numerik: 1-5
            $table->text('keterangan')->nullable(); // penjelasan detail skala
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kriteria');
    }
};