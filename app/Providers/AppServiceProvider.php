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
use Spatie\Activitylog\Models\Activity;

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

        // Prevent ANY activity log entry from being saved if the causer
        // (the logged-in user who triggered the action) is a student.
        // This covers automatic CRUD logging (LogsAllActivity) and manual
        // custom logs (LogsCustomActivity).
        Activity::creating(function (Activity $activity) {
            $causer = $activity->causer;

            if ($causer && method_exists($causer, 'role')) {
                $roleName = strtolower(optional($causer->role)->name ?? '');

                if ($roleName === 'student') {
                    return false; // cancels the save entirely
                }
            }
        });
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}