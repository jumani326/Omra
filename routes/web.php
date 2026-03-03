<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ChatbotController;
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

    // Inscription
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'choose'])->name('register.choose');
    Route::get('/register/agence', [\App\Http\Controllers\Auth\RegisterController::class, 'showAgenceForm'])->name('register.agence');
    Route::post('/register/agence', [\App\Http\Controllers\Auth\RegisterController::class, 'registerAgence'])->name('register.agence.store');
    Route::get('/register/pelerin', [\App\Http\Controllers\Auth\RegisterController::class, 'showPelerinForm'])->name('register.pelerin');
    Route::post('/register/pelerin', [\App\Http\Controllers\Auth\RegisterController::class, 'registerPelerin'])->name('register.pelerin.store');
    Route::get('/register/success', [\App\Http\Controllers\Auth\RegisterController::class, 'success'])->name('register.success');

    // Activation compte pèlerin (code 8 chiffres reçu par email)
    Route::get('/register/verify', [\App\Http\Controllers\Auth\RegisterController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [\App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('register.verify.store');
    Route::post('/register/resend-code', [\App\Http\Controllers\Auth\RegisterController::class, 'resendCode'])->name('register.resend-code');

    // Récupération mot de passe : 1) saisie email → 2) saisie code → 3) saisie nouveau mot de passe
    Route::get('/password/forgot', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/forgot', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/verify-code', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'verifyCode'])->name('password.verify-code');
    Route::get('/password/new-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showNewPasswordForm'])->name('password.new');
    Route::post('/password/update', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::post('/branch/switch', function (\Illuminate\Http\Request $request) {
        if (!auth()->user()->hasRole('agence')) {
            abort(403);
        }
        $request->validate(['branch_id' => 'nullable|exists:branches,id']);
        session(['current_branch_id' => $request->branch_id]);
        return redirect()->back();
    })->name('branch.switch');

    // Redirection dashboard selon le rôle (4 rôles : agence, ministere, guide, pelerin)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('agence')) {
            return redirect()->route('agence.dashboard');
        }
        if ($user->hasRole('ministere')) {
            return redirect()->route('ministere.dashboard');
        }
        if ($user->hasRole('guide')) {
            return redirect()->route('guide.dashboard');
        }
        if ($user->hasRole('pelerin')) {
            return redirect()->route('pelerin.dashboard');
        }
        return app(DashboardController::class)->index();
    })->name('dashboard');

    // ——— AGENCE ———
    Route::prefix('agence')->name('agence.')->middleware('role:agence')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Agence\AgenceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Agence\AgenceProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Agence\AgenceProfileController::class, 'update'])->name('profile.update');
        Route::get('/notifications', [\App\Http\Controllers\Agence\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Agence\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Agence\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    });

    // ——— MINISTÈRE ———
    Route::prefix('ministere')->name('ministere.')->middleware('role:ministere')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Ministere\MinistereDashboardController::class, 'index'])->name('dashboard');
        Route::post('/agencies/{agency}/validate', [\App\Http\Controllers\Ministere\MinistereAgencyController::class, 'validateAgency'])->name('agencies.validate');
        Route::post('/agencies/{agency}/suspend', [\App\Http\Controllers\Ministere\MinistereAgencyController::class, 'suspendAgency'])->name('agencies.suspend');
    });

    // ——— GUIDE ———
    Route::prefix('guide')->name('guide.')->middleware('role:guide')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Guide\GuideDashboardController::class, 'index'])->name('dashboard');
        Route::get('/messages', [\App\Http\Controllers\Guide\GroupMessageController::class, 'index'])->name('messages.index');
        Route::post('/messages', [\App\Http\Controllers\Guide\GroupMessageController::class, 'store'])->name('messages.store');
        Route::post('/checkin/{pilgrim}', [\App\Http\Controllers\Guide\GuideCheckinController::class, 'checkin'])->name('checkin');
        Route::post('/checkout/{pilgrim}', [\App\Http\Controllers\Guide\GuideCheckinController::class, 'checkout'])->name('checkout');
    });

    // ——— PÈLERIN ———
    Route::prefix('pelerin')->name('pelerin.')->middleware('role:pelerin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Pelerin\PelerinDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Pelerin\PelerinDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\Pelerin\PelerinDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/documents', [\App\Http\Controllers\Pelerin\PelerinDashboardController::class, 'documents'])->name('documents.index');
        Route::post('/documents/upload', [\App\Http\Controllers\Pelerin\PelerinDashboardController::class, 'uploadDocuments'])->name('documents.upload');

        // Paiement digital (modal : choix méthode → confirmation → enregistrement + fiche PDF)
        Route::get('/compte-marchand', [\App\Http\Controllers\Pelerin\PaiementClientController::class, 'compteMarchandByMethod'])->name('compte-marchand');
        Route::post('/transaction-digitale', [\App\Http\Controllers\Pelerin\PaiementClientController::class, 'store'])->name('transaction-digitale.store');
        Route::get('/transaction-digitale/{transaction}/fiche.pdf', [\App\Http\Controllers\Pelerin\PaiementClientController::class, 'fichePdf'])->name('fiche-paiement.pdf');

        // Notifications du pèlerin (lu / non lu)
        Route::get('/notifications', [\App\Http\Controllers\Pelerin\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Pelerin\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Pelerin\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

        // Messagerie avec le guide du groupe
        Route::get('/messages', [\App\Http\Controllers\Pelerin\GroupMessageController::class, 'index'])->name('messages.index');
        Route::post('/messages', [\App\Http\Controllers\Pelerin\GroupMessageController::class, 'store'])->name('messages.store');
    });

    // Espace client (Pèlerin) — catalogue forfaits publiés et postuler
    Route::prefix('client')->name('client.')->middleware('role:pelerin')->group(function () {
        Route::get('packages', [ClientController::class, 'packagesIndex'])->name('packages.index');
        Route::get('packages/{package}', [ClientController::class, 'packageShow'])->name('packages.show');
        Route::get('package/{package}/choose', [ClientController::class, 'choosePackage'])->name('package.choose');
        Route::post('package/{package}/store', [ClientController::class, 'storeChoosePackage'])->name('package.store');
    });

    // Chatbot (client)
    Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
    Route::get('/chatbot/session', [ChatbotController::class, 'session'])->name('chatbot.session');

    // Modules réservés à l'AGENCE (Pèlerins, Guides, Comptabilité, Visas, Forfaits, Hôtels, Utilisateurs)
    Route::middleware('role:agence')->group(function () {
        Route::get('pilgrims/export', [\App\Http\Controllers\PilgrimController::class, 'export'])->name('pilgrims.export');
        Route::resource('pilgrims', \App\Http\Controllers\PilgrimController::class);
        Route::resource('guides', \App\Http\Controllers\GuideController::class);
        Route::get('groups', [\App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
        Route::get('groups/create', [\App\Http\Controllers\GroupController::class, 'create'])->name('groups.create');
        Route::post('groups', [\App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
        Route::get('groups/{group}', [\App\Http\Controllers\GroupController::class, 'show'])->name('groups.show');
        Route::post('groups/{group}/add-pilgrims', [\App\Http\Controllers\GroupController::class, 'addPilgrims'])->name('groups.add-pilgrims');
        Route::post('groups/{group}/remove-pilgrim', [\App\Http\Controllers\GroupController::class, 'removePilgrim'])->name('groups.remove-pilgrim');
        Route::post('groups/{group}/send-list-to-guide', [\App\Http\Controllers\GroupController::class, 'sendListToGuide'])->name('groups.send-list-to-guide');
        Route::post('pilgrims/{pilgrim}/transfer', [\App\Http\Controllers\PilgrimController::class, 'transfer'])->name('pilgrims.transfer');
        Route::resource('packages', \App\Http\Controllers\PackageController::class);
        Route::post('packages/{package}/applications/{pilgrim}/approve', [\App\Http\Controllers\PackageController::class, 'approveApplication'])->name('packages.applications.approve');
        Route::post('packages/{package}/clone', [\App\Http\Controllers\PackageController::class, 'clone'])->name('packages.clone');
        Route::resource('hotels', \App\Http\Controllers\HotelController::class);
        Route::post('visas/{visa}/send-email', [\App\Http\Controllers\VisaController::class, 'sendEmail'])->name('visas.send-email');
        Route::resource('visas', \App\Http\Controllers\VisaController::class);
        Route::get('documents', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
        Route::post('documents/pilgrim/{pilgrim}/validate-dossier', [\App\Http\Controllers\DocumentController::class, 'validateDossier'])->name('documents.validate-dossier');
        Route::get('comptabilite', [\App\Http\Controllers\ComptabiliteController::class, 'index'])->name('comptabilite.index');
        Route::resource('compte-marchands', \App\Http\Controllers\CompteMarchandController::class);
        Route::resource('transaction-digitales', \App\Http\Controllers\TransactionDigitaleController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('transaction-digitales/{transaction_digitale}/valider', [\App\Http\Controllers\TransactionDigitaleController::class, 'valider'])->name('transaction-digitales.valider');
        Route::post('transaction-digitales/{transaction_digitale}/refuser', [\App\Http\Controllers\TransactionDigitaleController::class, 'refuser'])->name('transaction-digitales.refuser');
        Route::resource('payments', \App\Http\Controllers\PaymentController::class);
        Route::get('payments/{payment}/invoice', [\App\Http\Controllers\PaymentController::class, 'invoice'])->name('payments.invoice');
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });
});
