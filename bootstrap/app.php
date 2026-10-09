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

        $middleware->redirectUsersTo(function () {
            return strtolower(auth()->user()?->role?->name ?? '') === 'guest'
                ? route('referral')
                : route('gvc');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Your session expired. Please log in again.');
            }

            return redirect()->route('login')
                ->with('error', 'Your session expired. Please log in again.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout') || $request->is('*/logout')) {
                return redirect('/')->with('info', 'You have been redirected to the homepage.');
            }

            if ($request->is('livewire/update') || $request->is('livewire/*')) {
                return redirect('/');
            }
        });

        $exceptions->dontReport([
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        ]);
    })->create();