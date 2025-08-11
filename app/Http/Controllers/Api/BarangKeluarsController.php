<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluars;
use App\Models\DataPusats;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangKeluarsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangKeluar = BarangKeluars::latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $barangKeluar,
            'message' => 'List Barang Keluar',
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
        'tgl_keluar'  => 'required|date',
        'ket'         => 'nullable|string|max:255',
        'id_barang'   => 'required|integer|exists:data_pusats,id', 
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $barangKeluar = new BarangKeluars();
    $barangKeluar->kode_barang = $request->kode_barang;
    $barangKeluar->jumlah      = $request->jumlah;
    $barangKeluar->tgl_keluar  = $request->tgl_keluar;
    $barangKeluar->ket         = $request->ket;
    $barangKeluar->id_barang   = $request->id_barang;
    $barangKeluar->save();

    $dataPusat = DataPusats::find($request->id_barang);
    if ($dataPusat) {
        $dataPusat->stok -= $request->jumlah; 
        $dataPusat->save();
    }

    return response()->json([
        'success' => true,
        'data'    => [
            'barang_keluar' => $barangKeluar,
            'stok_terbaru' => $dataPusat->stok ?? null
        ],
        'message' => 'Barang Keluar berhasil disimpan dan stok diperbarui',
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
