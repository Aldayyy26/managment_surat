<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\StempelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\KopSuratController;
use App\Http\Controllers\SignatureController;


Route::get('/', [FrontController::class, 'index'])->name('frontend.index'); 


Route::get('/kop', [KopSuratController::class, 'index'])->name('kop.index');
Route::get('/kop/edit', [KopSuratController::class, 'edit'])->name('kop.edit');
Route::post('/kop/update', [KopSuratController::class, 'update'])->name('kop.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/admin/report', [ReportController::class, 'history'])->name('admin.report');
Route::middleware(['auth'])->group(function () {

    Route::resource('stempels', StempelController::class);

    Route::get('/approve', [ApproveController::class, 'index'])->name('approve.index');
    Route::post('/pengajuan_surat/{pengajuanSurat}/diterima', [ApproveController::class, 'approve']);
    Route::post('/pengajuan_surat/{pengajuanSurat}/ditolak', [ApproveController::class, 'reject']);
    
    Route::get('/pengajuan_surat/get-placeholders/{id}', [PengajuanSuratController::class, 'getPlaceholders'])->name('pengajuan_surat.get_placeholders');
    Route::resource('pengajuan_surat', PengajuanSuratController::class)->except(['edit']);
    Route::get('/pengajuan_surat/{id}/edit', [PengajuanSuratController::class, 'edit'])->name('pengajuan_surat.edit');
    Route::get('/pengajuan-surat/{id}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan_surat.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::resource('users', UserController::class);
});

Route::get('/signature/create', [SignatureController::class, 'create'])->name('signature.create');
Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');
Route::get('/signature', [SignatureController::class, 'index'])->name('signature.index');
Route::delete('/signature', [SignatureController::class, 'destroy'])->name('signature.destroy');

Route::get('/surats', [TemplateSuratController::class, 'index'])->name('surats.index');
Route::get('/surats/create', [TemplateSuratController::class, 'create'])->name('surats.create');
Route::post('/surats/upload', [TemplateSuratController::class, 'upload'])->name('surats.upload');
Route::get('/surats/{id}/select-placeholders', [TemplateSuratController::class, 'selectPlaceholdersForm'])->name('surats.selectPlaceholdersForm');
Route::post('/surats/{id}/select-placeholders', [TemplateSuratController::class, 'selectPlaceholders'])->name('surats.selectPlaceholders');
Route::get('/surats/{template}/edit', [TemplateSuratController::class, 'edit'])->name('surats.edit');
Route::put('/surats/{template}', [TemplateSuratController::class, 'update'])->name('surats.update');
Route::delete('/surats/{template}', [TemplateSuratController::class, 'destroy'])->name('surats.destroy');

// Auth route
require __DIR__.'/auth.php';
