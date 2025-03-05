<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Tighten\Ziggy\Ziggy;
use Closure;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => Auth::user() ? [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->getRoleNames(),
                ] : null,
                'token' => session('api_token'), 
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }

    public function handle(Request $request, Closure $next)
    {   
        // Debugging: Log the request and response
        \Log::info('Inertia Middleware: Request received', [
            'url' => $request->url(),
            'headers' => $request->headers->all(),
        ]);

        $response = $next($request);
        $response->headers->set('X-Inertia', true);
        $response->headers->set('Content-Type', 'text/html');

        \Log::info('Inertia Middleware: Response generated', [
            'status' => $response->status(),
            'headers' => $response->headers->all(),
        ]);

        return $response;
    }
}
