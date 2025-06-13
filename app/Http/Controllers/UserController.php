<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Display a list of all users
    public function index()
    {
        $users = User::with('role')->get(); // Eager load the role relationship
        return view('users.index', compact('users'));
    }

    // Show the form for creating a new user
    public function create()
    {
        $roles = Role::all(); // Get all available roles
        return view('users.create', compact('roles'));
    }

    // Store a newly created user in the database
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'nullable|exists:roles,id', // Validate that the role_id exists in the roles table
            //'password' => 'required|string|min:8|confirmed',
        ]);
    
        $roleId = $request->role_id; // Role selected by the admin
    
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make(strtoupper($request->first_name)), // You can change this logic for the default password
            'role_id' => $roleId, // Use the role_id passed from the form
        ]);

        if ($user) {
            return redirect()->route('users.index')->with('success', 'User registered successfully!');
        } else {
            return redirect()->back()->withErrors(['error' => 'User registration failed. Please try again.']);
        }
    }
    

    // Show the form for editing an existing user
    public function edit(User $user)
    {
        
    // Get all available roles
    $roles = Role::all();
    
    // Pass the user and roles to the view
    return view('users.edit', compact('user', 'roles'));
    }

    // Update an existing user's details
    public function update(Request $request, User $user)
{
    // Validate the request
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role_id' => 'required|exists:roles,id',
    ]);

    // Update the user
    $user->update($validated);

    // Redirect to the 'edit' page with the updated user
    return redirect()->route('users.index', ['user' => $user->id])->with('success', 'User updated successfully.');
}
// Show password change form
public function showChangePasswordForm()
{
    return view('users.change-password'); // Create this view to show the password change form
}

// Change password function
public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = Auth::user();

    if (Hash::check($request->current_password, $user->password)) {
        $user->password = Hash::make($request->new_password);
        $user->password_changed_at = now(); // Update the timestamp
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
    else{
        return redirect()->back()->withErrors('Current password is incorrect.');
    }         
}

 // Show the reset password form
 public function showResetPasswordForm($id)
 {
     $user = User::findOrFail($id);
     return view('users.reset-password', compact('user'));
 }

 // Handle the reset password
 public function resetPassword(Request $request, $id)
 {
     $request->validate([
         'password' => ['required', 'confirmed', 'min:8'],
     ]);

     $user = User::findOrFail($id);
     $user->password = Hash::make($request->password);
     $user->save();

     return redirect()->route('users.index')->with('success', 'Password reset successfully.');
 }

 //enabling and disabling users
 public function disableUser($id)
{
    $user = User::findOrFail($id);
    $user->status = 0; // Set to inactive
    $user->save();

    return redirect()->route('users.index')->with('success', 'User disabled successfully.');
}

public function enableUser($id)
{
    $user = User::findOrFail($id);
    $user->status = 1; // Set to active
    $user->save();

    return redirect()->route('users.index')->with('success', 'User enabled successfully.');
}


    // Delete a user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}