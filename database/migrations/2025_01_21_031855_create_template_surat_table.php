<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('lampiran');
            $table->string('perihal')->nullable();
            $table->string('kepada_yth')->nullable();
            $table->string('pembuka')->nullable();
            $table->string('teks_atas')->nullable();
            $table->json('konten');
            $table->string(' teks_bawah')->nullable();
            $table->string('penutup')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_surats'); // Perbaikan nama tabel agar sesuai dengan `create()`
    }
};
