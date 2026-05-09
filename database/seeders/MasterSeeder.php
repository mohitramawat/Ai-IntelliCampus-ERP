<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use App\Models\FeeStructure;
use App\Models\CourseUnitFee;

/**
 * MasterSeeder — Single source of truth for all reference data.
 *
 * Seeds in order:
 *   1. Campus
 *   2. Departments (4)
 *   3. Courses (5) — one per department, with unit types
 *   4. Batches — current active batches per course
 *   5. Subjects — per course per semester/year
 *   6. Fee Structures + Unit Fees — per course
 *
 * Does NOT touch:
 *   - Roles (RoleSeeder handles this)
 *   - Users (UserSeeder handles this)
 *   - Students (created via Writer portal)
 */
class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────
        // 1. CAMPUS
        // ─────────────────────────────────────────────────────────
        $campus = Campus::firstOrCreate(
            ['code' => 'PMTC-JPR'],
            [
                'name'      => 'Poddar Management and Technical Campus',
                'city'      => 'Jaipur',
                'state'     => 'Rajasthan',
                'is_active' => true,
            ]
        );
        $this->command->info('  ✔ Campus seeded');

        

        // ─────────────────────────────────────────────────────────
        // SUMMARY
        // ─────────────────────────────────────────────────────────
        $this->command->newLine();
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Campus',          Campus::count()],
               
            ]
        );
    }
}
