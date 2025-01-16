<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
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
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',  // Ensure password has a minimum length
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nim' => 'nullable|string|max:255',     // Nim is optional, used for certain roles
            'nidn' => 'nullable|string|max:255',    // Nidn is optional, used for certain roles
            'nip' => 'nullable|string|max:255',     // Nip is optional, used for certain roles
            'roles' => 'required|array',            // Roles should be an array
        ]);

        // Collect the form data
        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip');
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Hash the password if it was provided
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Create the user
        $user = User::create($data);

        // Assign roles to the user
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
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',  // Ensure password has a minimum length
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nim' => 'nullable|string|max:255',     // Nim is optional, used for certain roles
            'nidn' => 'nullable|string|max:255',    // Nidn is optional, used for certain roles
            'nip' => 'nullable|string|max:255',     // Nip is optional, used for certain roles
            'roles' => 'required|array',            // Roles should be an array
        ]);

        $user = User::findOrFail($id);
        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip');
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Hash the password if it was provided
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Update the user data
        $user->update($data);

        // Sync roles based on the input roles array
        $roles = Role::whereIn('id', $request->input('roles'))->pluck('name')->toArray();
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
