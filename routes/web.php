<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\GitHubConnectionController;

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
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/github', [GitHubConnectionController::class, 'edit'])->name('github.edit');
        Route::put('/github', [GitHubConnectionController::class, 'update'])->name('github.update');
        Route::post('/github/test', [GitHubConnectionController::class, 'test'])->name('github.test');
    });
});

require __DIR__.'/auth.php';
