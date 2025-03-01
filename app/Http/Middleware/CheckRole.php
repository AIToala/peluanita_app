<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!Auth::check()) {
            Log::info('User is not logged in');
            return redirect('login');
        }

        $user = Auth::user(); // Get the authenticated user
        $rolesArray = array_map('trim', explode('|', $roles)); // Split roles into an array
        if ($user->hasAnyRole($rolesArray) <= 0) { // Using Spatie's method
            Log::info('User does not have the required role(s)');
            return redirect()->route('dashboard')->with('error', 'No tienes autorización para este módulo');
        }
        return $next($request);
    }
}
