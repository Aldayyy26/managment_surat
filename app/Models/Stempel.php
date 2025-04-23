<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stempel extends Model
{
    use HasFactory;

    protected $table = 'stempels';
    
    protected $fillable = [
        'nama',
        'gambar',
    ];
}
