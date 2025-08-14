<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluars;
use App\Models\DataPusats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangKeluarsController extends Controller
{
    public function index()
    {
        $barangKeluar = BarangKeluars::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $barangKeluar,
            'message' => 'List Barang Keluar',
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:50',
            'jumlah'      => 'required|integer|min:1',
            'tgl_keluar'  => 'required|date',
            'ket'         => 'nullable|string|max:255',
            'id_barang'   => 'required|integer|exists:data_pusats,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Ambil data barang dari DataPusats
        $dataPusat = DataPusats::find($request->id_barang);

        // Cek stok cukup atau tidak
        if ($dataPusat->stok < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $dataPusat->stok,
            ], 400);
        }

        $barangKeluar = new BarangKeluars();
        $barangKeluar->kode_barang = $request->kode_barang;
        $barangKeluar->jumlah      = $request->jumlah;
        $barangKeluar->tgl_keluar  = $request->tgl_keluar;
        $barangKeluar->ket         = $request->ket;
        $barangKeluar->id_barang   = $request->id_barang;
        $barangKeluar->save();

        // Kurangi stok
        $dataPusat->stok -= $request->jumlah;
        $dataPusat->save();

        return response()->json([
            'success' => true,
            'data'    => [
                'barang_keluar' => $barangKeluar,
                'stok_terbaru' => $dataPusat->stok
            ],
            'message' => 'Barang Keluar berhasil disimpan dan stok diperbarui',
        ], 201);
    }

    public function show($id)
    {
        $barangKeluar = BarangKeluars::find($id);

        if (!$barangKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Keluar tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $barangKeluar,
            'message' => 'Detail Barang Keluar',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $barangKeluar = BarangKeluars::find($id);

        if (!$barangKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Keluar tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:50',
            'jumlah'      => 'required|integer|min:1',
            'tgl_keluar'  => 'required|date',
            'ket'         => 'nullable|string|max:255',
            'id_barang'   => 'required|integer|exists:data_pusats,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $dataPusat = DataPusats::find($barangKeluar->id_barang);

        // Kembalikan stok lama terlebih dahulu
        if ($dataPusat) {
            $dataPusat->stok += $barangKeluar->jumlah;
        }

        // Cek stok cukup atau tidak untuk jumlah baru
        if ($dataPusat && $dataPusat->stok < $request->jumlah) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi untuk update. Stok tersedia: ' . $dataPusat->stok,
            ], 400);
        }

        // Update barang keluar
        $barangKeluar->update($request->all());

        // Kurangi stok baru
        if ($dataPusat) {
            $dataPusat->stok -= $request->jumlah;
            $dataPusat->save();
        }

        return response()->json([
            'success' => true,
            'data'    => $barangKeluar,
            'message' => 'Barang Keluar berhasil diperbarui dan stok disesuaikan',
        ], 200);
    }

    public function destroy($id)
    {
        $barangKeluar = BarangKeluars::find($id);

        if (!$barangKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Keluar tidak ditemukan',
            ], 404);
        }

        $dataPusat = DataPusats::find($barangKeluar->id_barang);
        if ($dataPusat) {
            $dataPusat->stok += $barangKeluar->jumlah;
            $dataPusat->save();
        }

        $barangKeluar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang Keluar berhasil dihapus dan stok dikembalikan',
        ], 200);
    }
}
