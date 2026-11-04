<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $userRole = strtolower(trim((string) $user->role));

        $allowedRoles = array_map(
            fn ($role) => strtolower(trim((string) $role)),
            $roles
        );

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
