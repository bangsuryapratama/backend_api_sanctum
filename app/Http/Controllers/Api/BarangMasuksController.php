<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuks;
use App\Models\DataPusats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BarangMasuksController extends Controller
{
    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:50',
            'jumlah'      => 'required|integer|min:1',
            'tgl_masuk'   => 'required|date',
            'ket'         => 'nullable|string|max:255',
            'id_barang'   => 'required|integer|exists:data_pusats,id',
        ]);
    }

    public function index()
    {
        $barangMasuk = BarangMasuks::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $barangMasuk,
            'message' => 'List Barang Masuk',
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return DB::transaction(function () use ($request) {
            $barangMasuk = BarangMasuks::create($request->only([
                'kode_barang', 'jumlah', 'tgl_masuk', 'ket', 'id_barang'
            ]));

            $dataPusat = DataPusats::findOrFail($request->id_barang);
            $dataPusat->stok += $request->jumlah;
            $dataPusat->save();

            return response()->json([
                'success' => true,
                'data'    => [
                    'barang_masuk' => $barangMasuk,
                    'stok_terbaru' => $dataPusat->stok
                ],
                'message' => 'Barang masuk berhasil disimpan dan stok diperbarui',
            ], 201);
        });
    }

    public function show($id)
    {
        $barangMasuk = BarangMasuks::find($id);
        if (!$barangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Barang masuk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $barangMasuk,
            'message' => 'Detail Barang Masuk',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuks::find($id);
        if (!$barangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Barang masuk tidak ditemukan'
            ], 404);
        }

        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return DB::transaction(function () use ($request, $barangMasuk) {
            // Kurangi stok lama
            $dataPusatLama = DataPusats::find($barangMasuk->id_barang);
            if ($dataPusatLama) {
                $dataPusatLama->stok = max(0, $dataPusatLama->stok - $barangMasuk->jumlah);
                $dataPusatLama->save();
            }

            // Update data barang masuk
            $barangMasuk->update($request->only([
                'kode_barang', 'jumlah', 'tgl_masuk', 'ket', 'id_barang'
            ]));

            // Tambahkan stok baru
            $dataPusatBaru = DataPusats::find($request->id_barang);
            if ($dataPusatBaru) {
                $dataPusatBaru->stok += $request->jumlah;
                $dataPusatBaru->save();
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'barang_masuk' => $barangMasuk,
                    'stok_terbaru' => $dataPusatBaru->stok ?? null
                ],
                'message' => 'Barang masuk berhasil diperbarui dan stok diperbarui',
            ], 200);
        });
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuks::find($id);
        if (!$barangMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Barang masuk tidak ditemukan'
            ], 404);
        }

        return DB::transaction(function () use ($barangMasuk) {
            // Kurangi stok saat dihapus
            $dataPusat = DataPusats::find($barangMasuk->id_barang);
            if ($dataPusat) {
                $dataPusat->stok = max(0, $dataPusat->stok - $barangMasuk->jumlah);
                $dataPusat->save();
            }

            $barangMasuk->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk berhasil dihapus dan stok diperbarui',
            ], 200);
        });
    }
}
