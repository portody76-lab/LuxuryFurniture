<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        return view('contents.admin.manage-admin');
    }

    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore(Auth::id()),
            ],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan. Silakan pilih username lain.',
            'username.max' => 'Username maksimal 255 karakter.',
        ]);

        $user = User::find(Auth::id());
        $user->username = $request->username;
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('contents.admin.manage-admin')->with('success', 'Username berhasil diubah!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.'])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('contents.admin.manage-admin')->with('success', 'Password berhasil diubah!');
    }
}