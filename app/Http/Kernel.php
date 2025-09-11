<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    /**
     * Global HTTP middleware stack.
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustProxies::class,
        // \Illuminate\Http\Middleware\HandleCors::class,
        // \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        // \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // \App\Http\Middleware\TrimStrings::class,
        // \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // \App\Http\Middleware\EncryptCookies::class,
            // \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \App\Http\Middleware\VerifyCsrfToken::class,
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware.
     */
    protected $routeMiddleware = [
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        // เพิ่ม middleware อื่น ๆ ได้ที่นี่
    ];
}