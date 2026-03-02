<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur authentifié a l'un des rôles autorisés.
     *
     * @param  string  ...$roles  Noms des rôles (agence, ministere, guide, pelerin)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Non authentifié'], 401)
                : redirect()->route('login');
        }

        foreach ($roles as $role) {
            // Rôle principal (agence, ministere, guide, pelerin)
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }

            // Alias pour le rôle pèlerin côté client (« Pèlerin (Client) » dans certaines parties du code)
            if ($role === 'pelerin' && $request->user()->hasRole('Pèlerin (Client)')) {
                return $next($request);
            }
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Accès refusé'], 403)
            : abort(403, 'Accès refusé');
    }
}
