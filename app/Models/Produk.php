<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Menentukan nama tabel & primary key sesuai database
    protected $table = 'produk';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'nama_barang',
        'harga_barang',
        'deskripsi',
        'stok',
        'id_kategori',
    ];

    public $timestamps = false;

    // belongsTo: satu produk HANYA punya SATU kategori
    public function pesanan()
    {
        return $this->belongsToMany(
            Pesanan::class,
            'detail_pesanan',
            'id_produk',
            'id_pesanan'
        )->withPivot('jumlah');
    }
}
