<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    // PERBAIKAN: Tambahkan email dan no_telepon ke $fillable agar diizinkan oleh Laravel
    protected $fillable = ['nama_pelanggan', 'email', 'no_telepon', 'alamat'];
    public $timestamps = true;

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}
