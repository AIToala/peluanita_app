<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Auth\PersonalAccessToken;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = User::where('email', $request->email)->first();
        if (!$user || $user->estado != 1) {
            throw ValidationException::withMessages([
                'login' => 'Usuario inactivo o no existe',
            ]);
        }

        $request->session()->regenerate();

        $request->user()->tokens()->delete(); // Ensure no duplicate tokens
        $nconn = 'mysql';
        $agent = new \Jenssegers\Agent\Agent();
        $roles = $user->roles->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        $accessToken = $user->createToken('api-token')->plainTextToken;

        session(['api_token' => $accessToken]);
        return redirect()->intended(route('dashboard', absolute: false));
        
    }

    /**
     * Handle an incoming authentication request.
     */
    public function storeToken(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || $user->estado != 1) {
            throw ValidationException::withMessages([
                'login' => 'Usuario inactivo o no existe',
            ]);
        }
        $nconn = 'mysql';
        $agent = new \Jenssegers\Agent\Agent();
         // Generate Sanctum token for the user
        $roles = $user->roles->pluck('name');
        $accessToken = $user->createToken('api-token', $roles->toArray(), 43800, '1', $nconn, $request->ip(), $agent->platform(), $agent->version($agent->platform()), $agent->browser(), $agent->version($agent->browser()))->plainTextToken;
        $expires_at = PersonalAccessToken::getToken($accessToken)->expires_at;

        if ($request->expectsJson()) {
            return response()->json([
                'accessToken' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $expires_at,
                'userData' => $user,
            ], 200);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
