<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize students table for profile architecture.
     *
     * Fully idempotent — safe to run even if columns or constraints already exist.
     * Does NOT remove existing columns or data.
     */
    public function up(): void
    {
        // ── Add profile columns (skip if already exist) ──────────────────────
        $existing = DB::select("SHOW COLUMNS FROM `students`");
        $cols = array_map(fn($c) => $c->Field, $existing);

        Schema::table('students', function (Blueprint $table) use ($cols) {
            if (!in_array('category', $cols)) {
                $table->string('category', 50)->nullable()->after('admission_date');
            }
            if (!in_array('father_name', $cols)) {
                $table->string('father_name', 100)->nullable()->after('category');
            }
            if (!in_array('mother_name', $cols)) {
                $table->string('mother_name', 100)->nullable()->after('father_name');
            }
            if (!in_array('contact_number', $cols)) {
                $table->string('contact_number', 20)->nullable()->after('mother_name');
            }
            if (!in_array('address', $cols)) {
                $table->text('address')->nullable()->after('contact_number');
            }
        });

        // ── index(user_id) — skip if already exists ───────────────────────────
        $existingIndexes = DB::select("SHOW INDEX FROM `students` WHERE Key_name = 'students_user_id_index'");
        if (empty($existingIndexes)) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('user_id', 'students_user_id_index');
            });
        }

        // ── unique(user_id) — only if no duplicate user_ids exist ─────────────
        $duplicates = DB::select("SELECT user_id, COUNT(*) as cnt FROM `students` GROUP BY user_id HAVING cnt > 1");
        $uniqueIndexExists = DB::select("SHOW INDEX FROM `students` WHERE Key_name = 'students_user_id_unique'");

        if (empty($duplicates) && empty($uniqueIndexExists)) {
            Schema::table('students', function (Blueprint $table) {
                $table->unique('user_id', 'students_user_id_unique');
            });
        }

        // ── Harden students.user_id FK: CASCADE → RESTRICT ────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try { DB::statement('ALTER TABLE `students` DROP FOREIGN KEY `students_user_id_foreign`'); } catch (\Throwable $e) {}
        // Only add if not already set to RESTRICT
        $fkCheck = DB::select(
            "SELECT DELETE_RULE FROM information_schema.REFERENTIAL_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
             AND CONSTRAINT_NAME = 'students_user_id_foreign'"
        );
        if (empty($fkCheck) || $fkCheck[0]->DELETE_RULE !== 'RESTRICT') {
            DB::statement('ALTER TABLE `students` ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Restore user_id FK to CASCADE
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try { DB::statement('ALTER TABLE `students` DROP FOREIGN KEY `students_user_id_foreign`'); } catch (\Throwable $e) {}
        DB::statement('ALTER TABLE `students` ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::table('students', function (Blueprint $table) {
            try { $table->dropUnique('students_user_id_unique'); } catch (\Throwable $e) {}
            try { $table->dropIndex('students_user_id_index'); } catch (\Throwable $e) {}
            $table->dropColumn(['category', 'father_name', 'mother_name', 'contact_number', 'address']);
        });
    }
};
