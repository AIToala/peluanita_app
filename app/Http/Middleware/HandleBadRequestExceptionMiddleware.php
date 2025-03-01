<?php

namespace App\Http\Middleware;

use App\Exceptions\BadRequestException;
use App\Utils\LogUtils;
use Closure;
use Illuminate\Http\Request;

class HandleBadRequestExceptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (!empty($response->exception) && $response->exception instanceof BadRequestException) {
            $exception = $response->exception;
            if ($exception->shouldLog()) LogUtils::error($exception->getPrevious());
            return response()->json($exception->getResponseData(), $exception->getResponseCode());
        }
        return $response;
    }
}
