<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class WebToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('api')->guest()) {
            return response()->json(['code' => 401,'msg' => '未设置token'])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
        return $next($request);
    }
}

