<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Unduh card user
    Route::get('/profile/id-card', [ProfileController::class, 'downloadIdCard'])->name('profile.id.card');
    // selalu jalankan perintah php artisan route:cache setelah menambah atau modifikasi route

    // export excel data user
    // use App\Http\Controllers\ExcelController
    Route::get('/export-excel/users',
    [ExcelController::class, 'exportUsers'])->name('excel.export.users');
});

require __DIR__.'/auth.php';
