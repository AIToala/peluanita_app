<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JWTClaimCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $claim_name)
    {
        try {
            $token = $request->bearerToken();
            if ($token) {
                $nconn = PersonalAccessToken::getToken($token);
                if($nconn){
                    if ($nconn != $claim_name) return redirect('error-404'); // Prevent path mapping due Token overlap
                }
            }
        } catch (\Exception $e) {
            if(env('APP_DEBUG')){
                \Log::error($request->getClientIp());
                \Log::error($e);
            }else{
                \Log::channel('mongodbquerylog')->error($request->getClientIp());
                \Log::channel('mongodbquerylog')->error($e);
            }

        }
        return $next($request);
    }
}
