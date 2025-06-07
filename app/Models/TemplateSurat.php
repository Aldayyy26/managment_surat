<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $table = 'template_surats';

    protected $fillable = [
        'no_jenis_surat',
        'nama_surat',
        'file_path',
        'placeholders',
        'required_placeholders',
        'user_type',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'required_placeholders' => 'array',
    ];

    public function pengajuanSurats()
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
