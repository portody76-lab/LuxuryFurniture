<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role->role_name;
        
        // DEBUG - log role yang dicek
        // Log::info('RoleMiddleware - User: ' . $user->username . ', Role: ' . $userRole . ', Required: ' . implode(',', $roles));

        // Super admin bisa akses semua
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        // Cek apakah role user termasuk yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak untuk role: ' . $userRole . '. Required: ' . implode(',', $roles));
    }
}