<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        // Exclude CSRF verification for all routes
        $middleware->validateCsrfTokens(except: [
            '*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 404 errors gracefully for avis routes
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->is('hr/leaves/avis-depart/*') || $request->is('hr/leaves/avis-retour/*')) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Avis introuvable.'], 404);
                }
                return redirect()->route('hr.leaves.agents')
                    ->with('error', 'L\'avis demandé est introuvable.');
            }
        });
        
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('hr/leaves/avis-depart/*') || $request->is('hr/leaves/avis-retour/*')) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Page introuvable.'], 404);
                }
                return redirect()->route('hr.leaves.agents')
                    ->with('error', 'La page demandée est introuvable.');
            }
        });
    })->create();
