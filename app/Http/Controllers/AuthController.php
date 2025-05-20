<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find user by username
        $user = User::where('username', $validated['username'])->first();

        // Check if user exists and the password matches
        if ($user && Hash::check($validated['password'], $user->password)) {
            // Log the user in
            Auth::login($user);

            // Redirect to the dashboard
            return redirect()->route('newsfeed');
        }

        // If authentication fails, redirect back with an error message
        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

     // Add this logout method
     public function logout(Request $request)
     {
         // Log the user out
         Auth::logout();
 
         // Invalidate the session (optional, but recommended)
         $request->session()->invalidate();
         $request->session()->regenerateToken();
 
         // Redirect to login page after logout
         return redirect('/');
     }
}
