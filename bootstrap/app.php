<?php

use App\Http\Responses\ApiResponse;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $shouldRenderJson = static fn (Request $request): bool =>
            $request->expectsJson() || $request->is('api/*');

        $exceptions->render(function (ValidationException $e, Request $request) use ($shouldRenderJson) {
            if (! $shouldRenderJson($request)) {
                return null;
            }

            return ApiResponse::error('Error de validacion', 422, $e->errors());
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($shouldRenderJson) {
            if (! $shouldRenderJson($request)) {
                return null;
            }

            return ApiResponse::error('Recurso no encontrado', 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($shouldRenderJson) {
            if (! $shouldRenderJson($request)) {
                return null;
            }

            return ApiResponse::error('Metodo no permitido para esta ruta', 405);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($shouldRenderJson) {
            if (! $shouldRenderJson($request)) {
                return null;
            }

            if (config('app.debug')) {
                return ApiResponse::error('Error interno del servidor', 500, [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                ]);
            }

            return ApiResponse::error('Error interno del servidor', 500);
        });
    })->create();
