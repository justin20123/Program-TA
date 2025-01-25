<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorHasPengguna extends Model
{
    use HasFactory;

    protected $table = 'vendors_has_penggunas';

    protected $fillable = [
        'vendors_id',
        'penggunas_email',
        'penggunas_id',
    ];
}
