<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('denuncias.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Web Push Subscriptions
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe']);
});

require __DIR__.'/auth.php';
require __DIR__.'/denuncia/denuncia.php';
require __DIR__.'/denuncia/vote.php';
require __DIR__.'/map.php';
require __DIR__.'/category.php';
require __DIR__.'/stats.php';
require __DIR__.'/comments.php';
require __DIR__.'/notifications.php';

Route::middleware(['auth', 'role:A,M'])->group(function () {
    Route::get('/admin/dashboard', \App\Livewire\AdminDashboard::class)->name('admin.dashboard');
});
