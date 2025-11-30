<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    public function run()
    {
        // Get role IDs
        $tailorRole = \App\Models\StaffRole::where('role', 'Tailor')->first();
        $masterRole = \App\Models\StaffRole::where('role', 'Master')->first();
        $stitcherRole = \App\Models\StaffRole::where('role', 'Stitcher')->first();
        $designerRole = \App\Models\StaffRole::where('role', 'Designer')->first();
        $helperRole = \App\Models\StaffRole::where('role', 'Helper')->first();

        $staffMembers = [
            [
                'full_name' => 'John Doe',
                'phone' => '1234567890',
                'email' => 'john.doe@example.com',
                'role_id' => $tailorRole ? $tailorRole->id : null,
                'joining_date' => '2023-01-15',
                'address' => '123 Main Street, City, State',
                'shift_start_time' => '09:00:00',
                'shift_end_time' => '17:00:00',
                'profile_picture' => null,
                'id_proof' => null,
            ],
            [
                'full_name' => 'Jane Smith',
                'phone' => '0987654321',
                'email' => 'jane.smith@example.com',
                'role_id' => $masterRole ? $masterRole->id : null,
                'joining_date' => '2023-02-10',
                'address' => '456 Elm Avenue, City, State',
                'shift_start_time' => '08:30:00',
                'shift_end_time' => '16:30:00',
                'profile_picture' => null,
                'id_proof' => null,
            ],
            [
                'full_name' => 'Bob Johnson',
                'phone' => '1122334455',
                'email' => 'bob.johnson@example.com',
                'role_id' => $stitcherRole ? $stitcherRole->id : null,
                'joining_date' => '2023-03-05',
                'address' => '789 Oak Road, City, State',
                'shift_start_time' => '10:00:00',
                'shift_end_time' => '18:00:00',
                'profile_picture' => null,
                'id_proof' => null,
            ],
            [
                'full_name' => 'Alice Brown',
                'phone' => '5566778899',
                'email' => 'alice.brown@example.com',
                'role_id' => $designerRole ? $designerRole->id : null,
                'joining_date' => '2023-04-20',
                'address' => '321 Pine Lane, City, State',
                'shift_start_time' => '09:30:00',
                'shift_end_time' => '17:30:00',
                'profile_picture' => null,
                'id_proof' => null,
            ],
            [
                'full_name' => 'Charlie Wilson',
                'phone' => '9988776655',
                'email' => 'charlie.wilson@example.com',
                'role_id' => $helperRole ? $helperRole->id : null,
                'joining_date' => '2023-05-12',
                'address' => '654 Maple Drive, City, State',
                'shift_start_time' => '08:00:00',
                'shift_end_time' => '16:00:00',
                'profile_picture' => null,
                'id_proof' => null,
            ],
        ];

        foreach ($staffMembers as $staff) {
            if ($staff['role_id'] && !Staff::where('email', $staff['email'])->exists()) {
                Staff::create($staff);
            }
        }
    }
}