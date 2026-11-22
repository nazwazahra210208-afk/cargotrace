<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Cek apakah role user sesuai dengan yang diizinkan
        if ($user->role !== $role) {
            abort(403, 'Unauthorized action. Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}