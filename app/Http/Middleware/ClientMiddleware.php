<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ClientMiddleware
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
        if(Auth::check())
        {
            if(Auth::user()->role_id == User::ROLE_ID_CLIENT)
            {
                return $next($request);
            }
            else
            {
                return redirect('/login')->with('fail','You have Not Permission to access page');
            }
        }
        else
        {
            return redirect('/login')->with('status','You are not Login to AdminPanel');
        }
    }
}
