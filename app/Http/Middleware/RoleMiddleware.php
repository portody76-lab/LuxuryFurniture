<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // cek sudah login atau belum
        if (!Auth::check()) {
            return redirect('/login');
        }

        // ambil role user
        $userRole = Auth::user()->role->role_name;

        // cek apakah sesuai
        if ($userRole != $role) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}