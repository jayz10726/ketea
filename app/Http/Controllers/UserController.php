<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => ['required', Rules\Password::defaults()],
            'role'       => 'required|in:admin,storekeeper,staff',
            'department' => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:20',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'department' => $request->department,
            'phone'      => $request->phone,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete yourself.');
        $user->delete();
        return back()->with('success', 'User deleted.');
    }
}