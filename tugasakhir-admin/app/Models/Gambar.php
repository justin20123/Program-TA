<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gambar extends Model
{
    protected $fillable = [
        'url',
        'nota_id',
    ];

    public function nota()
    {
        return $this->belongsTo(Nota::class);
    }
}