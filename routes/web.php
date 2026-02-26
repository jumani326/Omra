<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

// Redirection racine vers dashboard si authentifié
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Module Pèlerins
    Route::resource('pilgrims', \App\Http\Controllers\PilgrimController::class);
    
    // Module Forfaits
    Route::resource('packages', \App\Http\Controllers\PackageController::class);
    Route::post('packages/{package}/clone', [\App\Http\Controllers\PackageController::class, 'clone'])->name('packages.clone');
    
    // Module Hôtels
    Route::resource('hotels', \App\Http\Controllers\HotelController::class);
    
    // Routes pour les autres modules (à créer aux Jours 4-5)
    // Route::resource('visas', VisaController::class);
    // Route::resource('payments', PaymentController::class);
    // Route::resource('users', UserController::class);
});
