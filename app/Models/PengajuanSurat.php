<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surats'; // Sesuaikan dengan nama tabel di database

    protected $fillable = [
        'user_id', 'template_id', 'konten', 'status', 'signature'
    ];
    

    protected $casts = [
        'konten' => 'array', // Jika 'konten' disimpan sebagai JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(TemplateSurat::class);
    }
}
