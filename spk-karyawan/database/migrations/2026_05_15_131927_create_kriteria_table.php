<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            // Set kriteria dipisah per jenis kepegawaian (revisi penguji)
            $table->enum('tipe', ['tetap', 'tidak_tetap'])->default('tetap');
            $table->enum('jenis', ['benefit', 'cost']);
            $table->decimal('bobot', 5, 2);
            $table->boolean('has_rentang')->default(false);
            $table->string('satuan_rentang', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};