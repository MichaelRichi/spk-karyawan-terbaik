<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * ================================================================
     * TABEL INTI INTEGRITAS DATA
     * ================================================================
     * Setiap periode menyimpan SNAPSHOT kriteria & bobot sendiri.
     * Saat direktur mengubah bobot atau kriteria di master, data
     * periode lama TIDAK berubah karena snapshot-nya tersimpan di sini.
     *
     * Alur:
     * 1. Direktur buat periode baru (draft)
     * 2. Sistem salin semua kriteria aktif ke periode_kriteria (snapshot)
     * 3. Direktur boleh UBAH bobot khusus untuk periode ini
     * 4. Saat periode dikunci (selesai), snapshot terkunci permanen
     * ================================================================
     */
    public function up(): void
    {
        Schema::create('periode_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            // nullOnDelete (bukan cascade): bila kriteria master dihapus, baris
            // snapshot periode lama TETAP ada (kriteria_id jadi NULL) sehingga
            // hasil ranking periode yang sudah selesai tidak berubah.
            $table->foreignId('kriteria_id')->nullable()->constrained('kriteria')->nullOnDelete();

            // ---- SNAPSHOT KRITERIA (tidak berubah walau master diubah) ----
            // tipe: menandai snapshot ini milik set kriteria karyawan tetap / tidak tetap
            $table->enum('tipe', ['tetap', 'tidak_tetap'])->default('tetap');
            $table->string('nama_kriteria', 100);
            $table->enum('jenis', ['benefit', 'cost']);
            $table->decimal('bobot', 5, 2);   // bobot (%) untuk periode ini, total harus = 100

            $table->timestamps();

            $table->unique(['periode_id', 'kriteria_id'], 'uq_periode_kriteria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_kriteria');
    }
};