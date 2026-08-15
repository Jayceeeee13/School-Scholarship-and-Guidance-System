<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LogAuthenticationActivity
{
    public function handleLogin(Login $event): void
    {
        activity('auth')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->event('login')
            ->log('User logged in');
    }

    public function handleLogout(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        activity('auth')
            ->causedBy($event->user)
            ->performedOn($event->user)
            ->event('logout')
            ->log('User logged out');
    }

    public function handleFailedLogin(Failed $event): void
    {
        activity('auth')
            ->event('failed_login')
            ->withProperties(['email' => $event->credentials['email'] ?? null])
            ->log('Failed login attempt');
    }
}