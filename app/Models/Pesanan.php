<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $primaryKey = 'id';

    protected $fillable = ['id_pelanggan', 'tanggal'];

    // Aktifkan timestamps jika tabel memiliki created_at/updated_at, atau set false jika tidak ada
    public $timestamps = true;

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }

    // Relasi pivot (Many-to-Many) ke Produk lewat tabel detail_pesanan
    public function produk()
    {
        return $this->belongsToMany(
            Produk::class,
            'detail_pesanan',
            'id_pesanan',
            'id_produk'
        )->withPivot('jumlah');
    }
}
