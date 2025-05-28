<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'manage surat',
            'manage history surat',
            'manage_users',
            'manage_apply_surat',
            'manage approval surat',
            'manage stempel',
            'manage report'
        ];

        // Create or find permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }

        $mahasiswaRole = Role::firstOrCreate(['name' => 'mahasiswa']);
        $mahasiswaPermissions = ['manage_apply_surat', 'manage history surat'];
        $mahasiswaRole->syncPermissions($mahasiswaPermissions);

        $dosenRole = Role::firstOrCreate(['name' => 'dosen']);
        $dosenPermissions = ['manage_apply_surat', 'manage history surat'];
        $dosenRole->syncPermissions($dosenPermissions);

        $adminprodiRole = Role::firstOrCreate(['name' => 'adminprodi']);
        $adminprodiPermissions = ['manage surat' , 'manage_users', 'manage stempel', 'manage report'];
        $adminprodiRole->syncPermissions($adminprodiPermissions);

        $kepalaprodiRole = Role::firstOrCreate(['name' => 'kepalaprodi']);
        $kepalaprodiPermissions = ['manage approval surat'];
        $kepalaprodiRole->syncPermissions($kepalaprodiPermissions);

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminPermissions = [ 'manage approval surat',
        'manage history surat',
        'manage_users',
        'manage_apply_surat',
        'manage stempel',
        'manage report'];
        $superAdminRole->syncPermissions($superAdminPermissions);

        $user = User::firstOrCreate([
            'email' => 'super@admin.com'
        ], [
            'name' => 'super admin',
            'nim' => '12345678',
            'avatar' => 'images/default-avatar.png',
            'password' => Hash::make('12345678'),
            
        ]);

        $user->assignRole($superAdminRole);
    }
}
