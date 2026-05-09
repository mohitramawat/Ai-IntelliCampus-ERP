<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Prevent duplicate attendance records for same student in same session
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->unique(['lecture_session_id', 'student_id'], 'uq_attendance_session_student');
            $table->index('student_id', 'idx_attendance_student_id');
        });

        // 2. Prevent duplicate student unit fees (student × fee_structure × unit)
        Schema::table('student_unit_fees', function (Blueprint $table) {
            $table->unique(['student_id', 'fee_structure_id', 'unit_number'], 'uq_student_unit_fee');
        });

        // 3. Prevent duplicate fine applications (installment × rule)
        Schema::table('installment_fines', function (Blueprint $table) {
            $table->unique(['student_unit_installment_id', 'fine_rule_id'], 'uq_installment_fine_rule');
        });

        // 4. Missing performance indexes
        Schema::table('lecture_sessions', function (Blueprint $table) {
            $table->index(['teacher_subject_assignment_id', 'lecture_date'], 'idx_session_assignment_date');
        });

        Schema::table('student_unit_installments', function (Blueprint $table) {
            $table->index('student_unit_fee_id', 'idx_installment_fee_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('user_id', 'idx_student_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropUnique('uq_attendance_session_student');
            $table->dropIndex('idx_attendance_student_id');
        });

        Schema::table('student_unit_fees', function (Blueprint $table) {
            $table->dropUnique('uq_student_unit_fee');
        });

        Schema::table('installment_fines', function (Blueprint $table) {
            $table->dropUnique('uq_installment_fine_rule');
        });

        Schema::table('lecture_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_session_assignment_date');
        });

        Schema::table('student_unit_installments', function (Blueprint $table) {
            $table->dropIndex('idx_installment_fee_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_student_user_id');
        });
    }
};
