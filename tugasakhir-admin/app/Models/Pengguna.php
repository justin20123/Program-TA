<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Pengguna extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $table = 'penggunas';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'nama',         
        'email',        
        'password',     
        'role',         
        'nomor_telepon',
        'saldo',  
    ];

    protected $hidden = [
        'password',
    ];

}
