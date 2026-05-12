<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class TelescopeAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Hanya check untuk path yang dimulai dengan /telescope (bukan /telescope-admin)
        if (!str_starts_with($request->getPathInfo(), '/telescope') || str_starts_with($request->getPathInfo(), '/telescope-admin')) {
            return $next($request);
        }

        // Untuk akses ke /telescope paths (kecuali /telescope-admin), check authorization
        $token = session('telescope_token');

        if (!$token) {
            return redirect('/telescope-admin/login');
        }

        // Verifikasi token
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable) {
            session()->forget(['telescope_token', 'telescope_user']);
            return redirect('/telescope-admin/login');
        }

        $tokenUser = $accessToken->tokenable;

        // Hanya SuperAdmin yang bisa akses
        if ($tokenUser->role->value !== Role::SUPERADMIN->value) {
            session()->forget(['telescope_token', 'telescope_user']);
            return redirect('/telescope-admin/login');
        }

        return $next($request);
    }
}

