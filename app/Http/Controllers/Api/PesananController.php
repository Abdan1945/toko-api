<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Exception;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    /**
     * Tampilkan semua daftar pesanan beserta relasi pelanggan dan produk.
     */
    public function index()
    {
        try {
            $pesanan = Pesanan::with([
                'pelanggan',
                'produk'
            ])->orderBy('id', 'asc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Data pesanan berhasil diambil.',
                'data' => $pesanan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan pesanan baru beserta multiple items ke tabel pivot.
     */
    public function store(Request $request)
    {
        try {
            // Validasi data (tanpa validasi status)
            $request->validate([
                'id_pelanggan' => 'required|exists:pelanggans,id',
                'tanggal' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.id_produk' => 'required|exists:produks,id',
                'items.*.jumlah' => 'required|integer|min:1',
            ]);

            // 1. Simpan data Master Pesanan
            $pesanan = new Pesanan;
            $pesanan->id_pelanggan = $request->id_pelanggan;
            $pesanan->tanggal = $request->tanggal;
            $pesanan->save();

            // 2. Format array items untuk attach ke tabel pivot Many-to-Many
            $produk = [];
            foreach ($request->items as $item) {
                $produk[$item['id_produk']] = [
                    'jumlah' => $item['jumlah']
                ];
            }

            // 3. Simpan relasi ke tabel pivot (detail_pesanan)
            $pesanan->produk()->attach($produk);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil ditambahkan.',
                'data' => $pesanan->load('pelanggan', 'produk'),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan detail satu pesanan spesifik.
     */
    public function show($id)
    {
        try {
            $pesanan = Pesanan::with(['pelanggan', 'produk'])->find($id);

            if (! $pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $pesanan,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perbarui data pesanan beserta sync item produknya.
     */
    public function update(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (! $pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            // Validasi input dari Vue.js
            $request->validate([
                'id_pelanggan' => 'required|exists:pelanggans,id',
                'tanggal' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.id_produk' => 'required|exists:produks,id',
                'items.*.jumlah' => 'required|integer|min:1',
            ]);

            // 1. Update data Master Pesanan
            $pesanan->id_pelanggan = $request->id_pelanggan;
            $pesanan->tanggal = $request->tanggal;
            $pesanan->save();

            // 2. Format array items
            $produk = [];
            foreach ($request->items as $item) {
                $produk[$item['id_produk']] = [
                    'jumlah' => $item['jumlah']
                ];
            }

            // 3. Sinkronisasi tabel pivot (mengganti item lama dengan item baru)
            $pesanan->produk()->sync($produk);

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil diperbarui.',
                'data' => $pesanan->load('pelanggan', 'produk'),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus pesanan beserta relasi item produknya.
     */
    public function destroy($id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (! $pesanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            // Lepaskan semua keterkaitan di tabel pivot lalu hapus master pesanan
            $pesanan->produk()->detach();
            $pesanan->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pesanan berhasil dihapus.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
