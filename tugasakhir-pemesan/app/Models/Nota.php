<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $fillable = [
        'waktu_transaksi',
        'status',
        'opsi_pengambilan',
        'alamat_pengambilan',
        'tanggal_selesai',
        'ulasan',
    ];
}