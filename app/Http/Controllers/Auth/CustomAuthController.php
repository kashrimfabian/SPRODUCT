<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
class CustomAuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');  
    }

    // Handle login request
    
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->status !== 1) { // Check if inactive
            Auth::logout();
            return redirect()->route('login')->withErrors(['error'=>'Your account is disabled. Please contact the administrator.']);
        }
        else{
         // Redirect to dashboard if active
        return redirect()->intended('/dashboard');
        }
        
    }else{

    return back()->withErrors(['Invalid credentials.']);
    }
}

        
    // Handle logout
    public function logout()
    {
        // Log out the user
        Auth::logout();
    
        // Invalidate the session
        request()->session()->invalidate();
    
        // Regenerate the CSRF token
        request()->session()->regenerateToken();
    
        // Add cache prevention headers and redirect to the login page
        return redirect()->route('login')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT', // Set a past expiration date
        ]);
    }
    


    
}
