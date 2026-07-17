<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Get all users, including the current admin
        $users = User::orderBy('name')->get();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:admin,viewer'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'Pengguna baru berhasil didaftarkan!');
    }

    public function toggleRole(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
        }

        $user->role = $user->role === 'admin' ? 'viewer' : 'admin';
        $user->save();

        return redirect()->back()->with('success', 'Peran pengguna berhasil diubah!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', __('Data pengguna :name berhasil diperbarui.', ['name' => $user->name]));
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', __('Kata sandi pengguna :name berhasil diperbarui.', ['name' => $user->name]));
    }
}
