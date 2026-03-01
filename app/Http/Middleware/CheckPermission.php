<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Vérifie que l'utilisateur authentifié a la permission donnée.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Non authentifié'], 401)
                : redirect()->route('login');
        }

        if (! $request->user()->can($permission)) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Permission refusée'], 403)
                : abort(403, 'Permission refusée');
        }

        return $next($request);
    }
}
