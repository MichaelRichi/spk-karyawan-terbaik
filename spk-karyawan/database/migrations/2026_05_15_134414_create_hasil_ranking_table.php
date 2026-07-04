<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hasil_ranking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();
            // tipe: ranking dipisah per jenis kepegawaian dalam satu periode
            $table->enum('tipe', ['tetap', 'tidak_tetap'])->default('tetap');
            $table->decimal('nilai_preferensi', 10, 6);
            $table->integer('ranking');
            $table->timestamps();
            $table->unique(['periode_id', 'karyawan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_ranking');
    }
};