<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpsiDetail extends Model
{
    protected $fillable = ['opsi', 'deskripsi', 'biaya_tambahan', 'detail_cetaks_id'];
}
