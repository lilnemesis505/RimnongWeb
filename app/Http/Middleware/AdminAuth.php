<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin_id') && !$request->is('login') && !$request->is('login/*')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
