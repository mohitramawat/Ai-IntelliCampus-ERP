<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Batch;
use App\Services\AuditService;
use App\Services\Fee\StudentFeeGenerationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class StudentCreationService
{
    protected AuditService $auditService;
    protected StudentFeeGenerationService $feeService;

    public function __construct(AuditService $auditService, StudentFeeGenerationService $feeService)
    {
        $this->auditService = $auditService;
        $this->feeService   = $feeService;
    }

    /**
     * Atomically create a user, assign student role, create student profile, and generate fees.
     *
     * Document uploads happen AFTER this method returns — they are NOT part of this transaction.
     *
     * @param array $userData        Keys: name, email, password
     * @param array $studentData     Keys: batch_id, roll_number, enrollment_number, admission_date,
     *                               category, father_name, mother_name, contact_number, address,
     *                               gender, date_of_birth
     * @return Student
     * @throws Exception
     */
    /**
     * Generate the auto-password for a new student.
     * Format: {COURSE_CODE}@{FirstName}{ContactNumber}
     * Example: MCA@Mohit8107233811
     */
    public static function generatePassword(string $courseCode, string $fullName, ?string $contact): string
    {
        $firstName = ucfirst(strtolower(explode(' ', trim($fullName))[0]));
        $contact   = preg_replace('/\D/', '', $contact ?? ''); // digits only
        return strtoupper($courseCode) . '@' . $firstName . $contact;
    }

    public function create(array $userData, array $studentData): Student
    {
        // Auto-generate password if not explicitly provided
        $batch      = Batch::with('course')->findOrFail($studentData['batch_id']);
        $courseCode = $batch->course->code;
        $plainPassword = $userData['password'] ?? self::generatePassword(
            $courseCode,
            $userData['name'],
            $studentData['contact_number'] ?? null
        );

        $student = DB::transaction(function () use ($userData, $studentData, $plainPassword) {

            // ── 1. Create User ────────────────────────────────────────────
            $user = User::create([
                'name'                 => $userData['name'],
                'email'                => $userData['email'],
                'password'             => Hash::make($plainPassword),
                'must_change_password' => true,
            ]);

            // ── 2. Assign 'student' role ──────────────────────────────────
            $user->assignRole('student');

            // ── 3. Create Student profile ─────────────────────────────────
            $student = Student::create([
                'user_id'           => $user->id,
                'batch_id'          => $studentData['batch_id'],
                'roll_number'       => $studentData['roll_number'],
                'enrollment_number' => $studentData['enrollment_number'],
                'admission_date'    => $studentData['admission_date'],
                'status'            => 'active',
                'is_active'         => true,
                'academic_status'   => 'active',
                'current_unit'      => 1,
                'category'          => $studentData['category']      ?? null,
                'father_name'       => $studentData['father_name']   ?? null,
                'mother_name'       => $studentData['mother_name']   ?? null,
                'contact_number'    => $studentData['contact_number'] ?? null,
                'address'           => $studentData['address']       ?? null,
                'gender'            => $studentData['gender']        ?? null,
                'date_of_birth'     => $studentData['date_of_birth'] ?? null,
            ]);

            // ── 4. Generate fees ──────────────────────────────────────────
            $this->feeService->generateForStudent($student);

            return $student;
        });

        $this->auditService->log(
            'student_created',
            $student,
            null,
            [
                'user_id'           => $student->user_id,
                'batch_id'          => $student->batch_id,
                'roll_number'       => $student->roll_number,
                'enrollment_number' => $student->enrollment_number,
            ]
        );

        // Attach generated password as transient (non-DB) property so controller can display it
        // This is intentional — it is NEVER stored in plain text in DB
        $student->plain_password = $plainPassword;

        return $student;
    }
}
