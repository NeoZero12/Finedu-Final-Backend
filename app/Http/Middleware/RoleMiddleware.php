<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // Menggunakan spread operator untuk menerima banyak role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        // 2. Cek apakah role user ada di dalam daftar $roles yang diizinkan
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            
            // Mengambil daftar role untuk pesan error agar lebih informatif (opsional)
            $allowedRoles = implode(' atau ', $roles);
            
            return response()->json([
                'message' => 'Akses ditolak. Halaman ini hanya untuk ' . $allowedRoles
            ], 403);
        }

        return $next($request);
    }
}