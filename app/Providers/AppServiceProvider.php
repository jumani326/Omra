<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\Pilgrim;
use App\Models\Package;
use App\Models\Visa;
use App\Models\Payment;
use App\Models\User;
use App\Models\Agency;
use App\Models\CompteMarchand;
use App\Models\TransactionDigitale;
use App\Policies\PilgrimPolicy;
use App\Policies\PackagePolicy;
use App\Policies\VisaPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\UserPolicy;
use App\Policies\AgencyPolicy;
use App\Policies\CompteMarchandPolicy;
use App\Policies\TransactionDigitalePolicy;
use App\Models\Guide;
use App\Policies\GuidePolicy;
use App\Models\Group;
use App\Policies\GroupPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Guide::class => GuidePolicy::class,
        Group::class => GroupPolicy::class,
        Pilgrim::class => PilgrimPolicy::class,
        Package::class => PackagePolicy::class,
        Visa::class => VisaPolicy::class,
        Payment::class => PaymentPolicy::class,
        User::class => UserPolicy::class,
        Agency::class => AgencyPolicy::class,
        \App\Models\Hotel::class => \App\Policies\HotelPolicy::class,
        CompteMarchand::class => CompteMarchandPolicy::class,
        TransactionDigitale::class => TransactionDigitalePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer l'Observer pour la timeline
        \App\Models\Pilgrim::observe(\App\Observers\PilgrimObserver::class);
    }
}
