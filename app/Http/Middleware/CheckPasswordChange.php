<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;  // Asegúrate de importar Auth

class CheckPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */ 
    /*public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->must_change_password && !$request->is('password/change')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }*/

    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
