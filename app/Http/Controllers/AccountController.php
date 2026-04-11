<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        return view('contents.manage-account');
    }

    public function updateUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return redirect()->route('manage-account')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find(Auth::id());
        $user->username = $request->username;
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('manage-account')->with('success', 'Username berhasil diubah!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('manage-account')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('manage-account')
                ->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('manage-account')->with('success', 'Password berhasil diubah!');
    }
}