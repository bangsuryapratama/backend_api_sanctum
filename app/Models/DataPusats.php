<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPusats extends Model
{
    use HasFactory;
    protected $fillable = ['id','kode_barang','nama','merk','foto','stok','created_at','updated_at'];
    public $timestamp = true;
    
    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuks::class, 'id_barang', 'id');
    }

    // public function barangKeluars()
    // {
    //     return $this->hasMany(BarangKeluar::class, 'id_barang', 'id');  
    // }

    // public function peminjamans()
    // {
    //     return $this->hasMany(Peminjamans::class, 'id_barang', 'id');
    // }

}
