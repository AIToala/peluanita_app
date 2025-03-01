<?php

namespace App\Http\Middleware;

use App\Models\Auth\PersonalAccessToken;
use Closure;
use DB;
use Illuminate\Http\Request;
use Log;

class QueryRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        /*Guardar logs de query con parametros del request*/
        if ($user->currentAccessToken()) {
            $nconn = PersonalAccessToken::getNconn($request->bearerToken());
            $username = $user->username;
            DB::listen(function ($query) use ($request, $nconn, $username): void {
                $query->sql = '/*'.$username.'#'.$nconn.'*/'.$query->sql;
                if (str_contains($query->sql, 'telescope_entries') or str_contains($query->sql, 'telescope_entries_tags') or str_contains($query->sql, 'telescope_monitoring')) {
                    return;
                }
                if (env('APP_DEBUG')) {
                    Log::channel('query')->info($request->getClientIp());
                    Log::channel('query')->info(
                        $query->sql,
                        $query->bindings,
                        $query->time
                    );
                } else {
                    Log::channel('mongodbquerylog')->info($request->getClientIp());
                    Log::channel('mongodbquerylog')->info(
                        $query->sql,
                        $query->bindings,
                        $query->time
                    );
                }
            });
        }

        return $next($request);
    }
}
