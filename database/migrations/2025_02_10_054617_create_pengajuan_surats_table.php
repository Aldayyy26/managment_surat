<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('template_id')->constrained('template_surats')->onDelete('cascade'); 
            $table->json('konten'); 
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('signature')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_surats');
    }
};
