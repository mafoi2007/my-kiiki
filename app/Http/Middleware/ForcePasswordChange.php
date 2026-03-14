<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs('password.change.form', 'password.change.update', 'logout')) {
            return $next($request);
        }

        return redirect()->route('password.change.form');
    }
}
