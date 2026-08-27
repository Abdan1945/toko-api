<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    // PERBAIKAN: Ubah id_pesanan menjadi id (sesuai kolom primary key MySQL)
    protected $primaryKey = 'id';

    protected $fillable = ['id_pelanggan', 'tanggal'];
    public $timestamps = false;

    // belongsTo: satu pesanan HANYA punya SATU pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }

    // belongsToMany lewat tabel detail_pesanan
    public function produk()
    {
        return $this->belongsToMany(
            Produk::class,
            'detail_pesanan',
            'id_pesanan', // Foreign key di tabel detail_pesanan
            'id_produk'
        )->withPivot('jumlah');
    }
}
