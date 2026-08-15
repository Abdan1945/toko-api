<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;



    protected $fillable = ['id_pelanggan', 'tanggal'];
		public $timestamps    = false;

    // belongsTo: satu pesanan HANYA punya SATU pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // belongsToMany: satu pesanan bisa punya BANYAK baris detail (item produk)
    // melalu table 'detail_pesanan' dan fk id_pesanan sebagai acuan pertama dan
    // fk id_produk sebagai acuan kedua yang ada di table pivot detail_pesanan
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
