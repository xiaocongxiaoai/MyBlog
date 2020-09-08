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
        //date_default_timezone_set('PRC');
        if (Auth::guard('api')->guest()) {
            return response()->json(['msg_code' => 1,'msg' => '未设置token'])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
        }
        return $next($request);
    }
}

