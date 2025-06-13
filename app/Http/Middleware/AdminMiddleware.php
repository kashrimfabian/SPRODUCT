<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user is an admin
        if (Auth::check() && Auth::user()->role_id===1 ) {
            return $next($request);
        }
            // Redirect unauthorized
        Auth::logout();
        return redirect()->route('login');
        

        
         
        
    }
}
