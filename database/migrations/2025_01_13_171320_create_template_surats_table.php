<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('template_surats', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('konten'); // Menyimpan HTML template surat
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('template_surats');
}

};
