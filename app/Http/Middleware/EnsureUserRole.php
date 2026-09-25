<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isActive()) {
            abort(403, 'Akun tidak aktif atau tidak terautentikasi.');
        }

        $allowedRoles = array_filter(
            array_map(UserRole::tryFrom(...), $roles),
            static fn (?UserRole $role): bool => $role !== null,
        );

        abort_unless($user->hasRole(...$allowedRoles), 403, 'Anda tidak memiliki akses ke bagian ini.');

        return $next($request);
    }
}
