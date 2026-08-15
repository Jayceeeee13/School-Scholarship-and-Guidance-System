<?php

namespace App\Providers;

use App\Models\ReferralInvitation;
use App\Observers\ReferralInvitationObserver;
use App\Models\ExamAttempt;
use App\Observers\ExamAttemptObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogAuthenticationActivity;

class AppServiceProvider extends ServiceProvider
{
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
        ReferralInvitation::observe(ReferralInvitationObserver::class);
        ExamAttempt::observe(ExamAttemptObserver::class);
        // Event::listen(Login::class, [LogAuthenticationActivity::class, 'handleLogin']);
        // Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handleLogout']);
        // Event::listen(Failed::class, [LogAuthenticationActivity::class, 'handleFailedLogin']);
    }

    public function shouldDiscoverEvents(): bool
{
    return false;
}
}
