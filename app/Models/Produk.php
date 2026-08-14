<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;


    protected $fillable = [
        'nama_barang', 'harga_barang', 'deskripsi', 'stok', 'id_kategori',
    ];
    public $timestamps    = true;


    // belongsTo: satu produk HANYA punya SATU kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori',);
    }

    // ada method lain dibawah ini
    ....

}
