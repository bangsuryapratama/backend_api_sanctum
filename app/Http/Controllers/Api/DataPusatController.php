<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPusats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DataPusatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datapusat = DataPusats::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $datapusat,
            'message' => 'List Pusat',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:50|unique:data_pusats,kode_barang',
            'nama'        => 'required|string|max:255',
            'merk'        => 'nullable|string|max:255',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $datapusat = new DataPusats();
        $datapusat->kode_barang = $request->kode_barang;
        $datapusat->nama        = $request->nama;
        $datapusat->merk        = $request->merk;
        $datapusat->stok        = $request->stok;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('datapusats', 'public');
            $datapusat->foto = $path;
        }

        $datapusat->save();

        return response()->json([
            'success' => true,
            'data'    => $datapusat,
            'message' => 'Data Pusat berhasil disimpan',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DataPusats $datapusat)
{
    return response()->json([
        'success' => true,
        'data'    => [
            'id'          => $datapusat->id,
            'kode_barang' => $datapusat->kode_barang,
            'nama'        => $datapusat->nama,
            'merk'        => $datapusat->merk,
            'stok'        => $datapusat->stok,
            'foto'        => $datapusat->foto,
            'created_at'  => $datapusat->created_at,
            'updated_at'  => $datapusat->updated_at,
        ],
        'message' => 'Detail Data Pusat',
    ], 200);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataPusats $datapusat)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|max:50|unique:data_pusats,kode_barang,' . $datapusat->id,
            'nama'        => 'required|string|max:255',
            'merk'        => 'nullable|string|max:255',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $datapusat->kode_barang = $request->kode_barang;
        $datapusat->nama        = $request->nama;
        $datapusat->merk        = $request->merk;
        $datapusat->stok        = $request->stok;

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($datapusat->foto && Storage::disk('public')->exists($datapusat->foto)) {
                Storage::disk('public')->delete($datapusat->foto);
            }
            $path = $request->file('foto')->store('datapusats', 'public');
            $datapusat->foto = $path;
        }

        $datapusat->save();

        return response()->json([
            'success' => true,
            'data'    => $datapusat,
            'message' => 'Data Pusat berhasil diperbarui',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataPusats $datapusat)
    {
        // Hapus foto jika ada
        if ($datapusat->foto && Storage::disk('public')->exists($datapusat->foto)) {
            Storage::disk('public')->delete($datapusat->foto);
        }

        $datapusat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Pusat berhasil dihapus',
        ], 200);
    }
}
