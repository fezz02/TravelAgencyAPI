<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED, 'User is not logged in.');
        }

        if (! auth()->user()->roles()->where('name', $role)->first()) {
            abort(Response::HTTP_FORBIDDEN, "User doesn't have permission to access this resource.");
        }

        return $next($request);
    }
}
