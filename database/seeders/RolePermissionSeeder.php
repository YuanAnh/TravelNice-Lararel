<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Tạo Permissions
        $permissions = [
            'view_tours', 'create_tours', 'edit_tours', 'delete_tours',
            'view_bookings', 'manage_bookings', 'approve_bookings',
            'view_users', 'manage_users',
            'view_reports', 'manage_settings',
            'manage_banners', 'manage_reviews',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Tạo Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Gán tất cả quyền cho Admin
        $adminRole->syncPermissions($permissions);

        // User chỉ có quyền xem cơ bản
        $userRole->givePermissionTo(['view_tours', 'view_bookings']);
    }
}