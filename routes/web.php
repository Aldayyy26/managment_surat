<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\StempelController;

// Halaman Utama
Route::get('/', [FrontController::class, 'index'])->name('frontend.index'); 

// Dashboard (Harus Login & Terverifikasi)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Semua route yang butuh login
Route::middleware(['auth'])->group(function () {
    // Approve Section

    Route::resource('stempels', StempelController::class);

    Route::get('/approve', [ApproveController::class, 'index'])->name('approve.index');
    Route::post('/pengajuan-surat/{pengajuanSurat}/diterima', [ApproveController::class, 'approve']);
    Route::post('/pengajuan-surat/{pengajuanSurat}/ditolak', [ApproveController::class, 'reject']);


    // Pengajuan Surat (CRUD + download)
    Route::resource('pengajuan-surat', PengajuanSuratController::class);
    Route::get('/pengajuan-surat/{pengajuanSurat}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan-surat.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::resource('users', UserController::class);
});

// Template Surat
Route::resource('surats', TemplateSuratController::class);
Route::get('/get-template-fields/{id}', [TemplateSuratController::class, 'getTemplateFields']);

// Auth route
require __DIR__.'/auth.php';
