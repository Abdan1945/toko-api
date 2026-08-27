<?php

namespace App\Http\Controllers;
use App\Models\Produk;

class FrontController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')->get();
        // dd($produk);
        return response()->json($produk);
    }
}
