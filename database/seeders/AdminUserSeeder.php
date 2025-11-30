<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'help40617@gmail.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'help40617@gmail.com',
                'password' => Hash::make('bk995031'),
            ]);
        }
    }
}
