<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopSurat extends Model
{
    use HasFactory;

    protected $table = 'kop_surat';

    protected $fillable = [
        'judul',
        'atas',
        'bawah',
    ];
}
