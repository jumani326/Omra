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
    Route::post('/branch/switch', function (\Illuminate\Http\Request $request) {
        if (!auth()->user()->hasRole('Super Admin Agence')) {
            abort(403);
        }
        $request->validate(['branch_id' => 'nullable|exists:branches,id']);
        session(['current_branch_id' => $request->branch_id]);
        return redirect()->back();
    })->name('branch.switch');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Module Pèlerins
    Route::resource('pilgrims', \App\Http\Controllers\PilgrimController::class);
    Route::post('pilgrims/{pilgrim}/transfer', [\App\Http\Controllers\PilgrimController::class, 'transfer'])->name('pilgrims.transfer');
    
    // Module Forfaits
    Route::resource('packages', \App\Http\Controllers\PackageController::class);
    Route::post('packages/{package}/clone', [\App\Http\Controllers\PackageController::class, 'clone'])->name('packages.clone');
    
    // Module Hôtels
    Route::resource('hotels', \App\Http\Controllers\HotelController::class);

    // Module Visas (Jour 4)
    Route::resource('visas', \App\Http\Controllers\VisaController::class);

    // Module Paiements / Finance (Jour 4)
    Route::resource('payments', \App\Http\Controllers\PaymentController::class);
    Route::get('payments/{payment}/invoice', [\App\Http\Controllers\PaymentController::class, 'invoice'])->name('payments.invoice');

    // Module Utilisateurs (Jour 2/5)
    Route::resource('users', \App\Http\Controllers\UserController::class);
});
