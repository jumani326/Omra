<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchScope
{
    /**
     * Handle an incoming request.
     * 
     * Isole les données par branche pour les utilisateurs non Super Admin
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Super Admin Agence et Superviseur Ministère voient tout
            if ($user->hasRole(['Super Admin Agence', 'Superviseur Ministériel National', 'Auditeur National'])) {
                return $next($request);
            }
            
            // Pour les autres rôles, isoler par branche
            if ($user->branch_id) {
                // Stocker la branche dans la session pour utilisation dans les scopes
                session(['current_branch_id' => $user->branch_id]);
            }
        }
        
        return $next($request);
    }
}
