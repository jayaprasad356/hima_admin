<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Shops;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('first_name', 'password');

        // Attempt login with Admin model
        if (Auth::guard('web')->attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('/admin'); // Redirect to the admin dashboard after login
        }

    
        return back()->withErrors(['first_name' => 'Invalid credentials']); // Adjust the error message as needed
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
