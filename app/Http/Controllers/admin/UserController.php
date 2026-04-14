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
            ->orderBy('id', 'asc')
            ->paginate(10);

        $totalUsers = User::count();
        $totalSuperAdmin = User::where('role_id', 3)->count(); // Super Admin (id=3)
        $totalAdmin = User::where('role_id', 1)->count();      // Admin (id=1)
        $totalOperator = User::where('role_id', 2)->count();   // Operator (id=2)
        $roles = Role::all();

        return view('contents.admin.users', compact('users', 'totalUsers', 'totalSuperAdmin', 'totalAdmin', 'totalOperator', 'roles'));
    }

    /**
     * Store user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role_id.required' => 'Role wajib dipilih.',
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

        // ✅ PERBAIKAN: redirect ke route yang benar
        return redirect()->route('contents.user-management.index')
            ->with('success', 'User berhasil ditambahkan! Email: ' . $request->email . ' | Password default: 12345678');
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'role_id' => 'required|exists:roles,id',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role_id.required' => 'Role wajib dipilih.',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'updated_by' => Auth::id(),
        ]);

        // ✅ PERBAIKAN: redirect ke route yang benar
        return redirect()->route('contents.user-management.index')
            ->with('success', 'User berhasil diupdate!');
    }

    public function destroy($id)
    {
        if ($id == Auth::id()) {
            // ✅ PERBAIKAN: redirect ke route yang benar
            return redirect()->route('contents.user-management.index')
                ->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user = User::findOrFail($id);
        $user->delete();

        // ✅ PERBAIKAN: redirect ke route yang benar
        return redirect()->route('contents.user-management.index')
            ->with('success', 'User berhasil dihapus!');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make('12345678'),
            'updated_by' => Auth::id(),
        ]);

        // ✅ PERBAIKAN: redirect ke route yang benar
        return redirect()->route('contents.user-management.index')
            ->with('success', 'Password user berhasil direset menjadi: 12345678');
    }

    /**
     * Toggle status user (Aktif/Nonaktif)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Cegah nonaktifkan akun sendiri
        if ($id == Auth::id()) {
            // ✅ PERBAIKAN: redirect ke route yang benar
            return redirect()->route('contents.user-management.index')
                ->with('error', 'Anda tidak bisa mengubah status akun sendiri!');
        }

        // Toggle status: aktif -> nonaktif, nonaktif -> aktif
        $user->status = !$user->status;
        $user->updated_by = Auth::id();
        $user->save();

        $statusText = $user->status ? 'diaktifkan' : 'dinonaktifkan';
        
        // ✅ PERBAIKAN: redirect ke route yang benar
        return redirect()->route('contents.user-management.index')
            ->with('success', "Status user berhasil {$statusText}!");
    }
}