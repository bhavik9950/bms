<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $this->seedAuthenticationRoles();
        $this->seedJobFunctionRoles();
    }

    /**
     * Seed authentication roles (for login/dashboard access)
     */
    private function seedAuthenticationRoles()
    {
        // Admin user - full system access
        User::firstOrCreate(
            ['email' => 'admin@boutique.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Technical Admin - same access as admin
        User::firstOrCreate(
            ['email' => 'tech@boutique.com'],
            [
                'name' => 'Technical Administrator',
                'password' => Hash::make('password'),
                'role' => 'technical_admin',
                'is_active' => true,
            ]
        );

        // Staff users - limited access
        $staffUsers = [
            ['email' => 'staff1@boutique.com', 'name' => 'Staff Member 1'],
            ['email' => 'staff2@boutique.com', 'name' => 'Staff Member 2'],
            ['email' => 'staff3@boutique.com', 'name' => 'Staff Member 3'],
        ];

        foreach ($staffUsers as $staffData) {
            User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Seed job function roles (for tailoring operations)
     */
    private function seedJobFunctionRoles()
    {
        // Update existing roles with better descriptions and status
        $existingRoles = [
            [
                'role' => 'Tailor',
                'description' => 'Responsible for sewing garments as per measurements.',
                'status' => true,
            ],
            [
                'role' => 'Master',
                'description' => 'Supervises tailoring staff and ensures quality stitching.',
                'status' => true,
            ],
            [
                'role' => 'Stitcher',
                'description' => 'Handles basic stitching and garment assembly tasks.',
                'status' => true,
            ],
            [
                'role' => 'Designer',
                'description' => 'Creates garment patterns and innovative designs.',
                'status' => true,
            ],
            [
                'role' => 'Helper',
                'description' => 'Assists in cutting, arranging, and supporting tailors.',
                'status' => true,
            ],
            [
                'role' => 'Ironman',
                'description' => 'Responsible for ironing and finishing garments before delivery.',
                'status' => true,
            ],
        ];

        // Add new roles if needed
        $newRoles = [
            [
                'role' => 'Cutter',
                'description' => 'Specializes in cutting fabric according to patterns.',
                'status' => true,
            ],
            [
                'role' => 'Quality Checker',
                'description' => 'Inspects finished garments for quality and standards.',
                'status' => true,
            ],
        ];

        $allRoles = array_merge($existingRoles, $newRoles);

        foreach ($allRoles as $roleData) {
            StaffRole::updateOrCreate(
                ['role' => $roleData['role']],
                [
                    'description' => $roleData['description'],
                    'status' => $roleData['status'],
                ]
            );
        }
    }
}