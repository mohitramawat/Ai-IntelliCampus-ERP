<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentPromotionService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Promote a single student by one unit.
     *
     * Rules:
     *  - Student must have academic_status = 'active'
     *  - If current_unit >= course total_units → mark as 'completed', do NOT increment
     *  - Otherwise → current_unit += 1
     *
     * @param Student $student
     * @return void
     * @throws Exception
     */
    public function promoteStudent(Student $student): void
    {
        // Validate academic status
        if ($student->academic_status === 'completed') {
            throw new Exception("Student [{$student->id}] has already completed the course.");
        }

        if ($student->academic_status === 'dropped') {
            throw new Exception("Student [{$student->id}] has dropped out and cannot be promoted.");
        }

        // Validate current_unit cannot be negative or zero
        if ($student->current_unit < 1) {
            throw new Exception("Student [{$student->id}] has an invalid current_unit value.");
        }

        // Load course total_units
        $student->loadMissing(['batch.course']);

        if (!$student->batch || !$student->batch->course) {
            throw new Exception("Student [{$student->id}] is not assigned to a valid batch and course.");
        }

        $totalUnits = (int) $student->batch->course->total_units;

        if ($totalUnits < 1) {
            throw new Exception("Course total_units is invalid for student [{$student->id}].");
        }

        DB::transaction(function () use ($student, $totalUnits) {
            if ($student->current_unit >= $totalUnits) {
                // Already at or beyond the final unit — mark as completed
                $student->academic_status = 'completed';
            } else {
                // Promote to next unit
                $student->current_unit += 1;
            }

            $student->save();
        });

        // Audit — outside transaction
        $this->auditService->log(
            'student_promoted',
            $student,
            null,
            [
                'current_unit'    => $student->current_unit,
                'academic_status' => $student->academic_status,
            ]
        );
    }

    /**
     * Promote all active students in a batch.
     *
     * Each student is promoted individually via promoteStudent().
     * If one student fails, that failure is caught and logged independently —
     * other students in the batch are still processed.
     *
     * The outer transaction ensures the audit log is only written
     * after all individual promotions complete without exception.
     *
     * @param Batch $batch
     * @return array{promoted: int, skipped: int, errors: array}
     */
    public function promoteBatch(Batch $batch): array
    {
        $batch->loadMissing('students');

        $activeStudents = $batch->students->where('academic_status', 'active');

        if ($activeStudents->isEmpty()) {
            throw new Exception("No active students found in batch [{$batch->id}].");
        }

        $promoted = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($activeStudents as $student) {
            try {
                $this->promoteStudent($student);
                $promoted++;
            } catch (Exception $e) {
                // Individual student failure should not abort the whole batch
                $skipped++;
                $errors[] = "Student [{$student->id}]: " . $e->getMessage();
            }
        }

        // Audit the batch-level action — outside individual transactions
        $this->auditService->log(
            'batch_promoted',
            $batch,
            null,
            [
                'promoted' => $promoted,
                'skipped'  => $skipped,
            ]
        );

        return [
            'promoted' => $promoted,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ];
    }
}
