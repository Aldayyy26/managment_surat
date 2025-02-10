<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengajuanSuratController;



Route::get('/', [FrontController::class, 'index'])->name ('frontend.index'); // Example for an "About" page
Route::get('/approve', [ApproveController::class, 'index'])->name('approve.index'); // Example for an "About" page
// Route to display available templates for the user // Example for an "About" page
Route::resource('surats', TemplateSuratController::class);
Route::get('/get-template-fields/{id}', [TemplateSuratController::class, 'getTemplateFields']);
Route::resource('users', UserController::class);
Route::middleware(['auth'])->group(function () {
    Route::resource('pengajuan-surat', PengajuanSuratController::class);
    Route::patch('/pengajuan-surat/{pengajuanSurat}/approve', [PengajuanSuratController::class, 'approve'])->name('pengajuan-surat.approve');
    Route::patch('/pengajuan-surat/{pengajuanSurat}/reject', [PengajuanSuratController::class, 'reject'])->name('pengajuan-surat.reject');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
