<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaffRole;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Insert demo data
        $roles = [
            [
                'role' => 'Tailor',
                'description' => 'Responsible for sewing garments as per measurements.'
            ],
            [
                'role' => 'Master',
                'description' => 'Supervises tailoring staff and ensures quality stitching.'
            ],
            [
                'role' => 'Stitcher',
                'description' => 'Handles basic stitching and garment assembly tasks.'
            ],
            [
                'role' => 'Designer',
                'description' => 'Creates garment patterns and innovative designs.'
            ],
            [
                'role' => 'Helper',
                'description' => 'Assists in cutting, arranging, and supporting tailors.'
            ],
            [
                'role' => 'Ironman',
                'description' => 'Responsible for ironing and finishing garments before delivery.'
            ],
        ];

        foreach ($roles as $roleData) {
            if (!StaffRole::where('role', $roleData['role'])->exists()) {
                StaffRole::create($roleData);
            }
        }
    }
}
