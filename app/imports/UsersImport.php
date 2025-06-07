<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek role dari excel, cari role di DB
        $role = Role::where('name', $row['role'])->first();

        // Jika role tidak ditemukan, bisa bikin role baru atau skip
        if (!$role) {
            // Optional: Buat role baru
            $role = Role::create(['name' => $row['role']]);
        }

        // Buat user baru atau update berdasarkan email
        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['name'],
                'nim' => $row['nim'] ?? null,
                'nidn' => $row['nidn'] ?? null,
                'nip' => $row['nip'] ?? null,
                'status' => $row['status'] ?? 'aktif',
                'semester' => $row['semester'] ?? null,
                'whatsapp_number' => $row['whatsapp_number'] ?? null,
                'password' => isset($row['password']) ? Hash::make($row['password']) : Hash::make('password123'),
            ]
        );

        // Assign role (syncRoles supaya bisa update)
        $user->syncRoles([$role->name]);

        return $user;
    }
}
