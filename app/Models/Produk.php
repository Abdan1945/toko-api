<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

<<<<<<< HEAD
    // Menyesuaikan dengan tabel yang sudah dibuat migration
    protected $table = 'produks';
    // protected $primaryKey = 'id_barang'; // dihapus karena pakai id biasa
=======
    protected $table = 'produks';
>>>>>>> 2b75705 (Project API Selesai)

    protected $fillable = [
        'nama_barang',
        'harga_barang',
        'deskripsi',
        'stok',
        'id_kategori',
    ];

<<<<<<< HEAD
    public $timestamps = true;   // diganti dari false

    // Relasi ke Kategori (ini yang dipakai di controller: with('kategori'))
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
=======
    public $timestamps = true;

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
>>>>>>> 2b75705 (Project API Selesai)
    }

    // Relasi ke Pesanan (many-to-many)
    public function pesanan()
    {
        return $this->belongsToMany(
            Pesanan::class,
            'detail_pesanan',
            'id_barang',       // foreign key di pivot ke produk
            'id_pesanan'
        )->withPivot('jumlah');
    }
}