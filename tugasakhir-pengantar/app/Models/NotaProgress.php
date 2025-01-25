<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaProgress extends Model
{
    use HasFactory;

    protected $table = 'notas_progress';

    protected $fillable = [
        'pemesanans_id',
        'notas_id',
        'urutan_progress',
        'waktu_progress',
        'progress',
        'url_ubah_file',
        'terverifikasi',
    ];

    // public function pemesanan()
    // {
    //     return $this->belongsTo(Pemesanan::class, 'pemesanans_id');
    // }

    // public function nota()
    // {
    //     return $this->belongsTo(Nota::class, 'notas_id');
    // }
}
