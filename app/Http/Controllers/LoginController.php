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
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = $user->role->role_name ?? null;

            // ========== CEK STATUS AKUN ==========
            if ($user->status == 0) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Status akun Anda nonaktif. Hubungi admin segera!',
                ])->onlyInput('username');
            }

            if ($role == 'admin') {
                return redirect()->route('contents.admin.dashboard');
            } else {
                return redirect()->route('contents.operator.dashboard');
            }
        }

        // Kirim error message
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda berhasil logout');
    }
}
