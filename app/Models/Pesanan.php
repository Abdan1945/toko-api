<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
=======
    protected $table = 'pesanan';                 // ← wajib (tanpa s)
    protected $primaryKey = 'id_pesanan';         // ← wajib
>>>>>>> 2b75705 (Project API Selesai)
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
            'id_pesanan',
<<<<<<< HEAD
            'id_barang'
        )->withPivot('jumlah');
    }
}
=======
            'id_barang'              // ← harus id_barang, bukan id_produk
        )->withPivot('jumlah');
    }
}
>>>>>>> 2b75705 (Project API Selesai)
