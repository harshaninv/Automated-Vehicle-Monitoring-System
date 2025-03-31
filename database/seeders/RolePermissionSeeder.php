<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            'view_vehicles',
            'register_vehicle',
            'request_access',
            'view_access_requests',
            'approve_access_request',
            'reject_access_request',
            'manage_users',
            'view_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'view_vehicles',
            'register_vehicle',
            'view_access_requests',
            'approve_access_request',
            'reject_access_request',
            'manage_users',
            'view_logs',
        ]);

        $securityRole = Role::firstOrCreate(['name' => 'security_personnel']);
        $securityRole->syncPermissions([
            'view_vehicles',
            'view_access_requests',
            'view_logs',
        ]);

        $vehicleOwnerRole = Role::firstOrCreate(['name' => 'vehicle_owner']);
        $vehicleOwnerRole->syncPermissions([
            'register_vehicle',
            'request_access',
        ]);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'phone' => '1234500000',
                'address' => 'Admin Address',
                'nic' => 'ADMIN123',
            ]
        );
        $admin->assignRole('admin');
    }
}
