<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->route('users.index')->with('success', 'Users imported successfully!');
    }
    public function index(Request $request)
    {
        $query = User::with('roles')->where('id', '!=', 1); // exclude user id 1

        // Search by name if provided
        if ($request->has('search_name') && $request->search_name != '') {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }

        // Search by nim if provided
        if ($request->has('search_nim') && $request->search_nim != '') {
            $query->where('nim', 'like', '%' . $request->search_nim . '%');
        }

        // Search by role if provided
        if ($request->has('search_role') && $request->search_role != '') {
            $searchRole = $request->search_role;
            $query->whereHas('roles', function ($q) use ($searchRole) {
                $q->where('name', 'like', '%' . $searchRole . '%');
            });
        }

        $users = $query->get();

        // Untuk dropdown role di filter nanti, ambil semua role
        $roles = Role::all();

        return view('Users.index', compact('users', 'roles'));
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
            'status' => 'required|in:aktif,nonaktif',
            'semester' => 'nullable|string|max:20',
            'roles' => 'required|string', 
            'whatsapp_number' => 'nullable|string|max:20|unique:users,whatsapp_number',
        ]);

        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip', 'status', 'semester', 'whatsapp_number');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user = User::create($data);

        // assignRole langsung string
        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
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
            'roles' => 'required|string', // ubah jadi string
            'whatsapp_number' => 'nullable|string|max:20|unique:users,whatsapp_number',
        ]);

        $user = User::findOrFail($id);
        $data = $request->only('name', 'email', 'nim', 'nidn', 'nip', 'status', 'semester', 'whatsapp_number');

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

        // syncRoles dengan array 1 elemen
        $user->syncRoles([$request->roles]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('Users.edit', compact('user', 'roles'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
