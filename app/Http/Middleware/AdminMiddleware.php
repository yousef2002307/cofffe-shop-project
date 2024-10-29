<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $is_admin = Auth::user()->is_admin;
        $is_super_admin = Auth::user()->is_super_admin;
        if($is_admin == 1 ){
            return $next($request);
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
       
    }
}
