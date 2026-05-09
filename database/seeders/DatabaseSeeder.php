<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,   // Roles must exist first
            UserSeeder::class,   // System users (admin, hod, accounts, writer, teacher)
            MasterSeeder::class, // Everything else: campus, departments, courses, batches, subjects, fees
        ]);
    }
}
