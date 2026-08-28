<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    // Sesuaikan fillable hanya dengan field yang diinput dari PelangganAdminView.vue
    protected $fillable = [
        'nama_pelanggan',
        'alamat'
    ];

    public $timestamps = true;

    // Relasi ke tabel Pesanan
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }
}
