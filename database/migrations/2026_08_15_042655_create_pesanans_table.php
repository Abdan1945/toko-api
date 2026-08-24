<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggans');
            $table->date('tanggal');
        });

        // table pivot untuk relasi many to many antara pesanan dan produk
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('id_pesanan')->nullable()->constrained('pesanans');
            $table->foreignId('id_produk')->nullable()->constrained('produks');
            $table->integer('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
        Schema::dropIfExists('detail_pesanan');
    }
};
