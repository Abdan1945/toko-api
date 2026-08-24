<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Exception;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        try {
<<<<<<< HEAD
            $pelanggan = Pelanggan::latest()->get();   // sekarang bisa pakai latest() lagi
=======
            // Diganti dari id_pelanggan menjadi id (sesuai kolom di database)
            $pelanggan = Pelanggan::orderBy('id', 'desc')->get();
>>>>>>> 2b75705 (Project API Selesai)

            return response()->json([
                'status'  => true,
                'message' => 'Data Pelanggan berhasil diambil',
                'data'    => $pelanggan,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_pelanggan' => 'required',
                'alamat'         => 'required',
            ]);

            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $request->nama_pelanggan,
                'alamat'         => $request->alamat,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'data pelanggan berhasil dibuat',
                'data'    => $pelanggan,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pelanggan = Pelanggan::find($id);
            if (! $pelanggan) {
                return response()->json(['status' => false, 'message' => 'data pelanggan tidak ada'], 404);
            }

            $request->validate([
                'nama_pelanggan' => 'required',
                'alamat'         => 'required',
            ]);

            $pelanggan->nama_pelanggan = $request->nama_pelanggan;
            $pelanggan->alamat         = $request->alamat;
            $pelanggan->save();

            return response()->json([
                'status'  => true,
                'message' => 'data pelanggan berhasil diedit',
                'data'    => $pelanggan,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);
            if (! $pelanggan) {
                return response()->json(['status' => false, 'message' => 'data pelanggan tidak ditemukan'], 404);
            }
            $pelanggan->delete();
            return response()->json(['status' => true, 'message' => 'data pelanggan berhasil dihapus'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}