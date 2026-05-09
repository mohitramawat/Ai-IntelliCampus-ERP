<?php

namespace App\Services\Fee;

use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\StudentUnitFee;
use App\Models\StudentUnitInstallment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class StudentFeeGenerationService
{
    /**
     * Generate student unit fees and installments automatically safely and idempotently.
     *
     * @param Student $student
     * @return void
     * @throws Exception
     */
    public function generateForStudent(Student $student, int $targetUnitNumber = null): void
    {
        DB::transaction(function () use ($student, $targetUnitNumber) {
            // Step A: Validate Preconditions
            $student->loadMissing(['batch.course']);
            $batch = $student->batch;

            if (!$batch || !$batch->course) {
                throw new Exception("Student must be assigned to a valid batch and course to generate fees.");
            }

            if (!$batch->start_year) {
                throw new Exception("Batch start year is required to determine the fee structure.");
            }

            // Target unit defaults to student's current unit if not specified
            $unitToGenerate = $targetUnitNumber ?? $student->current_unit;

            // Prevent Duplicate Generation for the specific unit
            $existingFee = StudentUnitFee::where('student_id', $student->id)
                ->where('unit_number', $unitToGenerate)
                ->lockForUpdate()
                ->first();

            if ($existingFee) {
                return; // Fee for this unit already generated.
            }

            // Step B: Get Fee Structure
            $feeStructure = FeeStructure::with('unitFees')
                ->where('course_id', $batch->course_id)
                ->where('effective_from_year', '<=', $batch->start_year)
                ->orderBy('effective_from_year', 'desc')
                ->first();

            if (!$feeStructure) {
                throw new Exception("No valid fee structure found for this course and batch start year.");
            }

            // Find the specific course unit fee
            $courseUnitFee = $feeStructure->unitFees->where('unit_number', $unitToGenerate)->first();
            
            if (!$courseUnitFee) {
                // If there's no fee defined for this unit, we gracefully exit.
                return;
            }

            $unitType = $batch->course->unit_type;
            $installmentCount = ($unitType == 'semester') ? 2 : 3;

            // Step F: Determine base date for due_date calculation
            // If it's unit 1, use admission date. If it's a promotion (unit > 1), use current date as base.
            $baseDate = ($unitToGenerate == 1 && $student->admission_date)
                ? Carbon::parse($student->admission_date)
                : now();

            $dueDateOffsets = ($unitType == 'semester')
                ? [1 => 2, 2 => 4]
                : [1 => 3, 2 => 6, 3 => 9];

            // Insert student_unit_fee
            $studentUnitFee = StudentUnitFee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $feeStructure->id,
                'unit_number' => $courseUnitFee->unit_number,
                'unit_name' => $courseUnitFee->unit_name,
                'unit_fee' => $courseUnitFee->unit_fee,
                'total_paid' => 0,
                'status' => 'pending',
                'is_active' => true,
            ]);

            // Generate Installments
            $totalUnitFee = (float) $courseUnitFee->unit_fee;
            $installmentAmount = round($totalUnitFee / $installmentCount, 2);

            for ($i = 1; $i <= $installmentCount; $i++) {
                $currentInstallmentAmount = $installmentAmount;
                
                if ($i === $installmentCount) {
                    $currentInstallmentAmount = $totalUnitFee - ($installmentAmount * ($installmentCount - 1));
                    $currentInstallmentAmount = round($currentInstallmentAmount, 2);
                }

                // E.g. "Installment 1", "Installment 2"
                $installmentName = "Installment " . $i;

                StudentUnitInstallment::create([
                    'student_unit_fee_id'  => $studentUnitFee->id,
                    'installment_number'   => $i,
                    'installment_amount'   => $currentInstallmentAmount,
                    'paid_amount'          => 0,
                    'status'               => 'pending',
                    'due_date'             => $baseDate->copy()->addMonths($dueDateOffsets[$i]),
                    'is_active'            => true,
                ]);
            }
        });
    }
}
