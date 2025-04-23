<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $table = 'template_surats';
    
    protected $fillable = [
        'judul',
        'lampiran',
        'perihal',
        'kepada_yth',
        'pembuka',
        'teks_atas',
        'konten',
        'teks_bawah',
        'penutup',
    ];

    protected $casts = [
        'konten' => 'array', 
    ];

    public function pengajuanSurats()
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
