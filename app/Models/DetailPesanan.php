<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'detail_pesanan';

    // Primary key tabel detail_pesanan
    protected $primaryKey = 'id_detail';

    // Kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'id_pesanan',
        'id_barang',
        'jumlah',
    ];

    // Nonaktifkan timestamps jika tabel tidak memiliki created_at & updated_at
    public $timestamps = false;

    // Relasi balik ke model Pesanan (Many to One)
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi balik ke model Produk (Many to One)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_barang', 'id_barang');
    }
}
