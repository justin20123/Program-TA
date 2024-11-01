<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    protected $fillable = [
        'nama_pengguna',
        'email_pengguna',
        'password_pengguna',
    ];
}
