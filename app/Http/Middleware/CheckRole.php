<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah user memiliki role yang sesuai
        if ($user->role !== $role) {
            // Redirect berdasarkan role user
            if ($user->role === 'warga') {
                return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman admin.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman warga.');
            }

            // Fallback jika role tidak dikenal
            return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        // Jika role sesuai, lanjutkan request
        return $next($request);
    }
}