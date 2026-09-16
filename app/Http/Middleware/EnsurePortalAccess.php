<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If this account gets archived while the user still has an active
        // session, kick them out immediately on their very next request —
        // don't wait for the session to naturally expire.
        if ($user && $user->isArchived()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'This account has been archived. Please contact the administrator.');
        }

        $role = strtolower($user?->role?->name ?? '');

        if ($user && !in_array($role, ['student', 'guest'])) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/admin')->with('error', 'Please use the admin panel for your account.');
        }

        return $next($request);
    }
}