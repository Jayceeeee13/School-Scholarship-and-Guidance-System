<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsEnrolled
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->user()->isEnrolled()) {
            return redirect('/gvc')->with('error', 'You must be enrolled to access this feature.');
        }

        return $next($request);
    }
}