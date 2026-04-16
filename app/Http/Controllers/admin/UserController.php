<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')
            ->when($request->search, function ($query, $search) {
                return $query->where('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderByRaw("FIELD(role_id, 3, 1, 2)")
            ->orderBy('id', 'asc')
            ->paginate(10);

        $totalUsers = User::count();
        $totalSuperAdmin = User::where('role_id', 3)->count();
        $totalAdmin = User::where('role_id', 1)->count();
        $totalOperator = User::where('role_id', 2)->count();
        $roles = Role::all();

        return view('contents.users', compact('users', 'totalUsers', 'totalSuperAdmin', 'totalAdmin', 'totalOperator', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make('12345678'),
            'role_id' => $request->role_id,
            'status' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('contents.users')
            ->with('success', 'User berhasil ditambahkan! Email: ' . $request->email . ' | Password default: 12345678');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('contents.users')
            ->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        if ($id == Auth::id()) {
            return redirect()->route('contents.users')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('contents.users')
            ->with('success', 'User berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('12345678'),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('contents.users')
            ->with('success', 'Password user berhasil direset menjadi: 12345678');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($id == Auth::id()) {
            return redirect()->route('contents.users')
                ->with('error', 'Anda tidak bisa mengubah status akun sendiri!');
        }

        $user->status = !$user->status;
        $user->updated_by = Auth::id();
        $user->save();

        $statusText = $user->status ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('contents.users')
            ->with('success', "Status user berhasil {$statusText}!");
    }
}