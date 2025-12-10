<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Support\Facades\Hash;

class LinkStaffToUsers extends Command
{
    protected $signature = 'staff:link-users';
    protected $description = 'Link existing staff to user accounts and assign job roles';

    public function handle()
    {
        // Check if roles exist first
        $this->ensureRolesExist();

        $staffMembers = [
            [
                'staff_code' => 'STF-001',
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@boutique.com',
                'role' => 'Master',
                'user_email' => 'staff1@boutique.com'
            ],
            [
                'staff_code' => 'STF-002',
                'name' => 'Priya Singh',
                'email' => 'priya@boutique.com',
                'role' => 'Stitcher',
                'user_email' => 'staff2@boutique.com'
            ],
            [
                'staff_code' => 'STF-003',
                'name' => 'Amit Patel',
                'email' => 'amit@boutique.com',
                'role' => 'Cutter',
                'user_email' => 'staff3@boutique.com'
            ],
        ];

        foreach ($staffMembers as $staffData) {
            // Create or update staff record
            $staff = Staff::updateOrCreate(
                ['staff_code' => $staffData['staff_code']],
                [
                    'full_name' => $staffData['name'],
                    'email' => $staffData['email'],
                    'role_id' => $this->getRoleId($staffData['role']),
                    'joining_date' => now(),
                    'status' => 1,
                ]
            );

            // Update user email to match staff email for consistency
            $user = User::where('email', $staffData['user_email'])->first();
            if ($user) {
                $user->update(['email' => $staffData['email']]);
                $this->info("✅ Linked staff {$staff->staff_code} to user account");
            } else {
                $this->warn("⚠️  User account not found for {$staffData['user_email']}");
            }
        }

        $this->info('🎉 Staff-user linking completed!');
    }

    private function ensureRolesExist()
    {
        $requiredRoles = ['Master', 'Stitcher', 'Cutter'];
        foreach ($requiredRoles as $roleName) {
            if (!StaffRole::where('role', $roleName)->exists()) {
                $this->error("❌ Required role '{$roleName}' not found!");
                $this->error("Please run: php artisan db:seed --class=RoleSeeder");
                exit(1);
            }
        }
    }

    private function getRoleId(string $roleName): string
    {
        $roleId = StaffRole::where('role', $roleName)->value('id');

        if (!$roleId) {
            throw new \Exception("Role '{$roleName}' not found. Make sure you run 'php artisan db:seed --class=RoleSeeder' first.");
        }

        return $roleId;
    }
}