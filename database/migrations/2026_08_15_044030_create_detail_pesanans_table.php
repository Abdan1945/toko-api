<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan di sini 'detail_pesanan', BUKAN 'pesanan'
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')->nullable()->constrained('pesanan', 'id_pesanan');
            $table->foreignId('id_barang')->nullable()->constrained('produk', 'id_barang');
            $table->integer('jumlah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
