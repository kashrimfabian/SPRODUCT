<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->role_id === 1 || Auth::user()->role_id === 2)) {
            return $next($request); // Allow access for admin and user roles
        }
        
        // Redirect unauthorized users
        Auth::logout();
        return redirect('/')->with('error', 'please login!!.');
        
        
         
    }
    
}
