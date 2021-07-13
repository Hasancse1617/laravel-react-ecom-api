<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
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
        return $next($request)
        ->header('Access-Control-Allow-Origin','*')
        ->header('Access-Control-Allow-Methods','PUT,GET,POST,DELETE,OPTIONS,PATCH')
        ->header('Access-Control-Allow-Headers','Origin, Content-Type, Accept, Authorization, X-Request-With,cache-control')
        ->header('Access-Control-Allow-Credentials','true');
    }
}
