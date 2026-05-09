<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $cols = DB::select('SHOW COLUMNS FROM lecture_sessions');
        $colNames = array_map(fn($c) => $c->Field, $cols);

        Schema::table('lecture_sessions', function (Blueprint $table) use ($colNames) {

            // ── Drop old assignment FK + column if still present ──────────
            if (in_array('teacher_subject_assignment_id', $colNames)) {
                // Drop FK constraint by name first
                try { $table->dropForeign('ls_tsa_id_foreign'); } catch (\Exception $e) {}
                try { $table->dropIndex(['teacher_subject_assignment_id']); } catch (\Exception $e) {}
                $table->dropColumn('teacher_subject_assignment_id');
            }

            // ── Add new columns only if they don't already exist ──────────
            if (!in_array('teacher_id', $colNames)) {
                $table->foreignId('teacher_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!in_array('subject_id', $colNames)) {
                $table->foreignId('subject_id')->after('teacher_id')->constrained('subjects')->cascadeOnDelete();
            }
            if (!in_array('batch_id', $colNames)) {
                $table->foreignId('batch_id')->after('subject_id')->constrained('batches')->cascadeOnDelete();
            }
        });

        // ── Add indexes safely ────────────────────────────────────────────
        $indexes = DB::select("SHOW INDEX FROM lecture_sessions");
        $indexNames = array_column($indexes, 'Key_name');

        Schema::table('lecture_sessions', function (Blueprint $table) use ($indexNames) {
            if (!in_array('lecture_sessions_teacher_id_index', $indexNames)) {
                $table->index('teacher_id');
            }
            if (!in_array('lecture_sessions_batch_id_index', $indexNames)) {
                $table->index('batch_id');
            }
            if (!in_array('lecture_sessions_subject_id_index', $indexNames)) {
                $table->index('subject_id');
            }
            if (!in_array('idx_session_date_period_batch', $indexNames)) {
                $table->index(['lecture_date', 'period_number', 'batch_id'], 'idx_session_date_period_batch');
            }
        });

        // ── Unique constraint on attendance_records (session+student) ─────
        $arIndexes = DB::select("SHOW INDEX FROM attendance_records");
        $arIndexNames = array_column($arIndexes, 'Key_name');

        if (!in_array('attendance_records_session_student_unique', $arIndexNames)) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->unique(
                    ['lecture_session_id', 'student_id'],
                    'attendance_records_session_student_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('lecture_sessions', function (Blueprint $table) {
            try { $table->dropForeign(['teacher_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['subject_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['batch_id']); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_session_date_period_batch'); } catch (\Exception $e) {}
            try { $table->dropColumn(['teacher_id', 'subject_id', 'batch_id']); } catch (\Exception $e) {}
        });
    }
};
