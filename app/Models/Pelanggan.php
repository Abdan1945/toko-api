<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';          // diganti dari 'pelanggan'
    // protected $primaryKey = 'id_pelanggan'; // dihapus, karena pakai id biasa

    public $timestamps = true;               // diganti dari false, karena ada timestamps

    protected $fillable = ['nama_pelanggan', 'alamat'];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}