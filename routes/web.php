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
});

Route::resource('surats', TemplateSuratController::class);

Route::get('/get-template-fields/{id}', [TemplateSuratController::class, 'getTemplateFields']);
// User Management (CRUD)
Route::resource('users', UserController::class);
// Pengajuan Surat (CRUD & Approval)

Route::middleware(['auth'])->group(function () {
    Route::resource('pengajuan-surat', PengajuanSuratController::class);
    Route::post('/pengajuan-surat/{pengajuanSurat}/approve', [ApproveController::class, 'approve'])->name('approve.surat');
    Route::post('/pengajuan-surat/{pengajuanSurat}/reject', [ApproveController::class, 'reject'])->name('reject.surat');
    Route::get('/pengajuan-surat/{pengajuanSurat}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan-surat.download');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
