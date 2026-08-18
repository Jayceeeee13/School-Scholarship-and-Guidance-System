<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LogAuthenticationActivity
{
    public function handleLogin(Login $event): void
    {
        if ($this->isStudent($event->user)) {
            return;
        }

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

        if ($this->isStudent($event->user)) {
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
        // Failed logins have no resolved user/role yet (credentials may not
        // even match a real account), so these are always logged regardless.
        activity('auth')
            ->event('failed_login')
            ->withProperties(['email' => $event->credentials['email'] ?? null])
            ->log('Failed login attempt');
    }

    protected function isStudent(?object $user): bool
    {
        if (! $user) {
            return false;
        }

        return strtolower($user->role?->name ?? '') === 'student';
    }
}