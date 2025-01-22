<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kpiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\HistorySuratController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\UserSuratController;


Route::get('/', [FrontController::class, 'index'])->name('frontend.index'); // Example for an "About" page
Route::get('/approve', [ApproveController::class, 'index'])->name('approve.index'); // Example for an "About" page
// Route to display available templates for the user // Example for an "About" page
Route::get('/history', [HistorySuratController::class, 'index'])->name('history.index'); // Example for an "About" page
Route::resource('surats', TemplateSuratController::class);

Route::middleware(['auth', 'can:manage surat'])->group(function () {
    
});

Route::middleware(['auth', 'can:manage_users'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users/surat/create', [UserSuratController::class, 'create'])->name('users.surat.create');
    Route::post('users/surat/store', [UserSuratController::class, 'store'])->name('users.surat.store');
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
