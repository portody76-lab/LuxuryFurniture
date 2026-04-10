<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

public function login(Request $request)
{
    if (!Auth::attempt($request->only('username', 'password'))) {
        return back()->with('error', 'Username atau password salah');
    }

    $role = auth()->user()->role->role_name;

    if ($role == 'admin') {
        return redirect()->route('contents.dashboard'); // pakai named route
    } else {
        return redirect()->route('operator.dashboard');
    }
}

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}