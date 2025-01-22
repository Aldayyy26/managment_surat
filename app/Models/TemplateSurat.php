<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $table = 'template_surat';

    protected $fillable = [
        'judul', 'konten'
    ];

    protected $casts = [
        'konten' => 'array', // This will automatically cast konten to an array
    ];
}
