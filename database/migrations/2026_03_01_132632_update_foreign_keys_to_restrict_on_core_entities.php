<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 1. Convert remaining MyISAM tables to InnoDB (required for FK enforcement).
     * 2. Enforce RESTRICT foreign keys on all core ERP entities via raw SQL.
     *
     * Idempotent: drops FKs first (ignores errors), then re-adds as RESTRICT.
     * Safe forward-only migration. Does NOT drop tables or modify columns.
     */
    public function up(): void
    {
        // PHASE 1: Convert MyISAM → InnoDB (safe to run even if already InnoDB)
        DB::statement('ALTER TABLE `subjects` ENGINE = InnoDB');
        DB::statement('ALTER TABLE `teacher_subject_assignments` ENGINE = InnoDB');
        DB::statement('ALTER TABLE `lecture_sessions` ENGINE = InnoDB');
        DB::statement('ALTER TABLE `attendance_records` ENGINE = InnoDB');

        // PHASE 2: Enforce RESTRICT FKs
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop all target FKs first (idempotent — ignore if they don't exist)
        $drops = [
            'ALTER TABLE `fee_structures` DROP FOREIGN KEY `fee_structures_course_id_foreign`',
            'ALTER TABLE `batches` DROP FOREIGN KEY `batches_course_id_foreign`',
            'ALTER TABLE `students` DROP FOREIGN KEY `students_batch_id_foreign`',
            'ALTER TABLE `subjects` DROP FOREIGN KEY `subjects_course_id_foreign`',
            'ALTER TABLE `teacher_subject_assignments` DROP FOREIGN KEY `teacher_subject_assignments_subject_id_foreign`',
            'ALTER TABLE `lecture_sessions` DROP FOREIGN KEY `ls_tsa_id_foreign`',
            'ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_lecture_session_id_foreign`',
            'ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_student_id_foreign`',
        ];

        foreach ($drops as $sql) {
            try { DB::statement($sql); } catch (\Throwable $e) { /* FK may not exist yet — continue */ }
        }

        // Add all FKs as RESTRICT
        DB::statement('ALTER TABLE `fee_structures` ADD CONSTRAINT `fee_structures_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `batches` ADD CONSTRAINT `batches_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `students` ADD CONSTRAINT `students_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `subjects` ADD CONSTRAINT `subjects_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `teacher_subject_assignments` ADD CONSTRAINT `teacher_subject_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `lecture_sessions` ADD CONSTRAINT `ls_tsa_id_foreign` FOREIGN KEY (`teacher_subject_assignment_id`) REFERENCES `teacher_subject_assignments`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_lecture_session_id_foreign` FOREIGN KEY (`lecture_session_id`) REFERENCES `lecture_sessions`(`id`) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop all RESTRICT FKs
        $drops = [
            'ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_student_id_foreign`',
            'ALTER TABLE `attendance_records` DROP FOREIGN KEY `attendance_records_lecture_session_id_foreign`',
            'ALTER TABLE `lecture_sessions` DROP FOREIGN KEY `ls_tsa_id_foreign`',
            'ALTER TABLE `teacher_subject_assignments` DROP FOREIGN KEY `teacher_subject_assignments_subject_id_foreign`',
            'ALTER TABLE `subjects` DROP FOREIGN KEY `subjects_course_id_foreign`',
            'ALTER TABLE `students` DROP FOREIGN KEY `students_batch_id_foreign`',
            'ALTER TABLE `batches` DROP FOREIGN KEY `batches_course_id_foreign`',
            'ALTER TABLE `fee_structures` DROP FOREIGN KEY `fee_structures_course_id_foreign`',
        ];

        foreach ($drops as $sql) {
            try { DB::statement($sql); } catch (\Throwable $e) {}
        }

        // Restore as CASCADE
        DB::statement('ALTER TABLE `fee_structures` ADD CONSTRAINT `fee_structures_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_lecture_session_id_foreign` FOREIGN KEY (`lecture_session_id`) REFERENCES `lecture_sessions`(`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `attendance_records` ADD CONSTRAINT `attendance_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `lecture_sessions` ADD CONSTRAINT `ls_tsa_id_foreign` FOREIGN KEY (`teacher_subject_assignment_id`) REFERENCES `teacher_subject_assignments`(`id`) ON DELETE CASCADE');
        DB::statement('ALTER TABLE `teacher_subject_assignments` ADD CONSTRAINT `teacher_subject_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
