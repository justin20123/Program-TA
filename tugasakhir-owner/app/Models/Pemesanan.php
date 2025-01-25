<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'penggunas_email',
        'jumlah',
        'url_file',
        'harga_cetaks_id',
        'harga_cetaks_id_jenis_bahan_cetaks',
        'vendors_id',
        'notas_id',
    ];
}
