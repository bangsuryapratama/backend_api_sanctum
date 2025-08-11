<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPusats;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
