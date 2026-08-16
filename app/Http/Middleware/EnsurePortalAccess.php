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