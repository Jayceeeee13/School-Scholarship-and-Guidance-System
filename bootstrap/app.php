<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'enrolled' => \App\Http\Middleware\EnsureUserIsEnrolled::class,
            'portal'   => \App\Http\Middleware\EnsurePortalAccess::class,
        ]);

        $middleware->trustProxies(at: '*');

        // Logged-in users who open /login or /register are sent back here,
        // so pressing Back after login never shows the login form.
        $middleware->redirectUsersTo(function () {
            return strtolower(auth()->user()?->role?->name ?? '') === 'guest'
                ? route('referral')
                : route('gvc');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            return redirect()->route('login')
                ->with('error', 'Your session expired. Please log in again.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout') || $request->is('*/logout')) {
                return redirect('/')->with('info', 'You have been redirected to the homepage.');
            }

            // Stray GET requests to Livewire's internal AJAX endpoint — typically
            // bots, crawlers, or mangled shared links (e.g. carrying an fbclid
            // param), never a real user action. Redirect quietly to the homepage
            // instead of showing an error page.
            if ($request->is('livewire/update') || $request->is('livewire/*')) {
                return redirect('/');
            }
        });

        // Keep these out of logs/error dashboards entirely — both cases above
        // are already handled gracefully, so there's nothing actionable to report.
        $exceptions->dontReport([
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        ]);
    })->create();