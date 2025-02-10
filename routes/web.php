<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengajuanSuratController;

// Halaman Utama
Route::get('/', [FrontController::class, 'index'])->name('frontend.index'); 

// Dashboard (Harus Login & Terverifikasi)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Approve Section (Harus Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/approve', [ApproveController::class, 'index'])->name('approve.index');
    Route::patch('/approve/{pengajuanSurat}/approve', [ApproveController::class, 'approve'])->name('approve.approve');
    Route::patch('/approve/{pengajuanSurat}/reject', [ApproveController::class, 'reject'])->name('approve.reject');
    Route::get('/pengajuan-surat/{pengajuanSurat}/pdf', [ApproveController::class, 'generatePDF'])->name('pengajuan.pdf');
});


// Template Surat (CRUD)
Route::resource('surats', TemplateSuratController::class);
Route::get('/get-template-fields/{id}', [TemplateSuratController::class, 'getTemplateFields']);

// User Management (CRUD)
Route::resource('users', UserController::class);

// Pengajuan Surat (CRUD & Approval)
Route::middleware(['auth'])->group(function () {
    Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('pengajuan-surat.index');
    Route::get('/pengajuan-surat/create', [PengajuanSuratController::class, 'create'])->name('pengajuan-surat.create');
    Route::post('/pengajuan-surat', [PengajuanSuratController::class, 'store'])->name('pengajuan-surat.store');
    Route::get('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'show'])->name('pengajuan-surat.show');
    Route::get('/pengajuan-surat/{pengajuanSurat}/edit', [PengajuanSuratController::class, 'edit'])->name('pengajuan-surat.edit');
    Route::put('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'update'])->name('pengajuan-surat.update');
    Route::get('/pengajuan-surat/{pengajuanSurat}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan-surat.download');
});

// Profil User
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Menggunakan Auth Routes
require __DIR__.'/auth.php';
