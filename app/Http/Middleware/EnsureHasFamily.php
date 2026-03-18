<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasFamily
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Jika user belum login, biarkan middleware 'auth' yang menangani
        if (!$user) {
            return $next($request);
        }

        // 2. Kondisi: User BELUM punya keluarga
        if (!$user->family_id) {
            // Jika dia mencoba akses selain halaman onboarding, paksa redirect
            if (!$request->routeIs('onboarding') && !$request->routeIs('family.*')) {
                return redirect()->route('onboarding')->with('info', 'Silakan buat atau gabung keluarga terlebih dahulu.');
            }
        } 
        
        // 3. Kondisi: User SUDAH punya keluarga
        else {
            // Jika dia iseng mencoba akses halaman onboarding lagi, kembalikan ke dashboard
            if ($request->routeIs('onboarding')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}