<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuestController extends Controller
{
    /**
     * Show the guest registration form.
     */
    public function showForm()
    {
        return view('guest');
    }

    /**
     * Handle guest form submission, save to users table, then redirect to login.
     */
    public function submitForm(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Look up the 'guest' role — safely falls back to null if it doesn't exist
        $guestRole = Role::where('name', 'guest')->first();

        // Create and save the guest user to the users table
        User::create([
            'name'     => $request->full_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $guestRole?->id ?? null,
        ]);

        // Redirect to login with a success message
        return redirect()->route('login')->with('success', 'Account created! Please log in to continue.');
    }
}