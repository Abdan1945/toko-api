<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengecekan: Hanya buat tabel jika 'pesanan' BELUM ADA
        if (!Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table) {
                $table->id('id_pesanan');
                $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggan', 'id_pelanggan');
                $table->date('tanggal');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
