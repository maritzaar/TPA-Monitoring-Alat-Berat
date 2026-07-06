<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Get all users except current admin
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function toggleRole(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', __('Anda tidak dapat mengubah peran Anda sendiri.'));
        }

        $user->role = $user->role === 'admin' ? 'viewer' : 'admin';
        $user->save();

        return redirect()->back()->with('success', __('Peran pengguna :name berhasil diubah menjadi :role.', [
            'name' => $user->name,
            'role' => $user->role === 'admin' ? 'Admin/Operator' : 'Viewer'
        ]));
    }
}
