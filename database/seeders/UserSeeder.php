<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@intellicampus.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Writer User',
                'email' => 'writer@intellicampus.com',
                'role' => 'writer',
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@intellicampus.com',
                'role' => 'teacher',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // syncRoles is idempotent — safe to call even if role already assigned
            $user->syncRoles([$userData['role']]);
        }
    }
}
