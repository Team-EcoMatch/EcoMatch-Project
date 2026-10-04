<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUsuarioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->estado == 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Tu cuenta ha sido desactivada. Contacta al jefe de tu empresa.');
        }

        return $next($request);
    }
}
