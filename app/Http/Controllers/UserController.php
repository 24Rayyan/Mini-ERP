<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Ditambahkan import Rule yang benar

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->get();
        $roles = Role::all();
        return view('management.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        // 2. Ambil data Role
        $role = Role::findOrFail($validated['role_id']);

        // 3. Simpan User Baru
        User::create([
            'username'  => $validated['username'],
            'name'      => ucfirst(str_replace('_', ' ', $role->name)),
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $role->id,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan!');
    }

    // 1. Edit (Ganti Role & Username)
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id'  => ['required', 'exists:roles,id'],
        ]);

        $user->update([
            'username' => $request->username,
            'role_id'  => $request->role_id,
        ]);

        return back()->with('success', "Data user {$user->username} berhasil diperbarui.");
    }

    // 2. Toggle Status Aktif / Nonaktif
    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menonaktifkan akun sendiri!');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun user {$user->username} berhasil {$status}.");
    }

    // 3. Reset Password Custom
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password untuk user {$user->username} berhasil direset.");
    }

    // 4. Delete User
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun sendiri!');
        }

        $username = $user->username;
        $user->delete();

        return back()->with('success', "User {$username} berhasil dihapus dari sistem.");
    }
}