<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuks;
use App\Models\DataPusats;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangMasuksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangMasuk = BarangMasuks::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $barangMasuk,
            'message' => 'List Barang Masuk',
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'kode_barang' => 'required|string|max:50',
        'jumlah'      => 'required|integer|min:1',
        'tgl_masuk'   => 'required|date',
        'ket'         => 'nullable|string|max:255',
        'id_barang'   => 'required|integer|exists:data_pusats,id', 
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $barangMasuk = new BarangMasuks();
    $barangMasuk->kode_barang = $request->kode_barang;
    $barangMasuk->jumlah      = $request->jumlah;
    $barangMasuk->tgl_masuk   = $request->tgl_masuk;
    $barangMasuk->ket         = $request->ket;
    $barangMasuk->id_barang   = $request->id_barang;
    $barangMasuk->save();

    $dataPusat = DataPusats::find($request->id_barang);
    if ($dataPusat) {
        $dataPusat->stok += $request->jumlah; 
        $dataPusat->save();
    }

    return response()->json([
        'success' => true,
        'data'    => [
            'barang_masuk' => $barangMasuk,
            'stok_terbaru' => $dataPusat->stok ?? null
        ],
        'message' => 'Barang masuk berhasil disimpan dan stok diperbarui',
    ], 201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
