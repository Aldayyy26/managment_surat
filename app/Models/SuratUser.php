<?php

// app/Models/SuratUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_surat_id',
        'judul',
        'konten',
    ];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function templateSurat()
    {
        return $this->belongsTo(TemplateSurat::class);
    }
}
