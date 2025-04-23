<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller {

    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search by name if provided
        if ($request->has('search_name') && $request->search_name != '') {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        // Search by nim if provided
        if ($request->has('search_nim') && $request->search_nim != '') {
            $query->where('nim', 'like', '%' . $request->search_nim . '%');
        }

        // Fetch the filtered users
        $users = $query->get();

        return view('Users.index', compact('users'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('Users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nim' => 'nullable|string|max:255',
            'nidn' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif', // Validasi enum
            'semester' => 'nullable|string|max:20',
            'roles' => 'required|array',
        ]);

        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip', 'status', 'semester');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user = User::create($data);
        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('Users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nim' => 'nullable|string|max:255',
            'nidn' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'semester' => 'nullable|string|max:20',
            'roles' => 'required|array',
        ]);

        $user = User::findOrFail($id);
        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip', 'status', 'semester');

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
        $user->syncRoles($roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete avatar if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
