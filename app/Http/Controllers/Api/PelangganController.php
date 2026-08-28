<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Exception;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Ambil daftar semua pelanggan.
     */
    public function index()
    {
        try {
            // Mengurutkan pelanggan berdasarkan ID ascending
            $pelanggan = Pelanggan::orderBy('id', 'asc')->get();

            return response()->json([
                'status'  => true,
                'message' => 'Data pelanggan berhasil diambil.',
                'data'    => $pelanggan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan data pelanggan baru.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input dari Vue.js (nama_pelanggan & alamat wajib)
            $request->validate([
                'nama_pelanggan' => 'required|string|max:255',
                'alamat'         => 'required|string',
                'email'          => 'nullable|email|unique:pelanggans,email',
                'no_telepon'     => 'nullable|string|unique:pelanggans,no_telepon',
            ]);

            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $request->nama_pelanggan,
                'alamat'         => $request->alamat,
                'email'          => $request->email ?? null,
                'no_telepon'     => $request->no_telepon ?? null,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data pelanggan berhasil dibuat.',
                'data'    => $pelanggan,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil detail satu pelanggan berdasarkan ID.
     */
    public function show($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pelanggan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data'   => $pelanggan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perbarui data pelanggan berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pelanggan tidak ditemukan.',
                ], 404);
            }

            // Validasi update
            $request->validate([
                'nama_pelanggan' => 'required|string|max:255',
                'alamat'         => 'required|string',
                'email'          => 'nullable|email|unique:pelanggans,email,' . $id,
                'no_telepon'     => 'nullable|string|unique:pelanggans,no_telepon,' . $id,
            ]);

            // Mengupdate kolom yang diperbolehkan saja
            $pelanggan->update([
                'nama_pelanggan' => $request->nama_pelanggan,
                'alamat'         => $request->alamat,
                'email'          => $request->email ?? $pelanggan->email,
                'no_telepon'     => $request->no_telepon ?? $pelanggan->no_telepon,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data pelanggan berhasil diperbarui.',
                'data'    => $pelanggan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus data pelanggan berdasarkan ID.
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::find($id);

            if (!$pelanggan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data pelanggan tidak ditemukan.',
                ], 404);
            }

            $pelanggan->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data pelanggan berhasil dihapus.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
