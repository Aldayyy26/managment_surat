<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User yang mengajukan surat
            $table->foreignId('template_id')->constrained('template_surats')->onDelete('cascade'); // Template surat yang digunakan
            $table->json('konten'); // Jawaban/form dari user
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending'); // Status pengajuan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_surats');
    }
};
