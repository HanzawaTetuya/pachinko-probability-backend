<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // リクエスト前の処理
        Log::info('TestMiddleware: Before handling the request.');

        $response = $next($request);

        // リクエスト後の処理
        Log::info('TestMiddleware: After handling the request.');

        return $response;
    }
}
