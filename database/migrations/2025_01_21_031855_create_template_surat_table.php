<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_surat');
            $table->string('file_path');
            $table->json('placeholders')->nullable();
            $table->json('required_placeholders')->nullable();
            $table->enum('user_type', ['mahasiswa', 'dosen']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_surats'); // Perbaikan nama tabel agar sesuai dengan `create()`
    }
};
