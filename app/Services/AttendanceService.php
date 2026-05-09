<?php

namespace App\Services;

use App\Models\LectureSession;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\Batch;
use App\Models\Subject;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Start a new lecture session.
     * Teacher, subject and batch are passed directly — no assignment table needed.
     *
     * @param int    $teacherId
     * @param int    $subjectId
     * @param int    $batchId
     * @param int    $periodNumber
     * @param float  $teacherLat
     * @param float  $teacherLong
     * @return LectureSession
     * @throws Exception
     */
    public function startSession(
        int $teacherId,
        int $subjectId,
        int $batchId,
        int $periodNumber,
        float $teacherLat,
        float $teacherLong
    ): LectureSession {

        $session = DB::transaction(function () use (
            $teacherId, $subjectId, $batchId, $periodNumber, $teacherLat, $teacherLong
        ) {
            $today = Carbon::today()->toDateString();
            $now   = Carbon::now();

            // Step 1: Validate subject belongs to the batch's course
            $subject = Subject::findOrFail($subjectId);
            $batch   = Batch::findOrFail($batchId);

            if ($subject->course_id !== $batch->course_id) {
                throw new Exception(
                    "Subject [{$subject->name}] does not belong to the course of this batch. " .
                    "Cross-course subject misuse is not allowed."
                );
            }

            // Step 2: Prevent duplicate active session for same batch + period + today
            $duplicate = LectureSession::where('batch_id', $batchId)
                ->where('lecture_date', $today)
                ->where('period_number', $periodNumber)
                ->where('status', 'active')
                ->exists();

            if ($duplicate) {
                throw new Exception(
                    "An active session already exists for this batch on Period {$periodNumber} today."
                );
            }

            // Step 3: Create session with direct ownership
            return LectureSession::create([
                'teacher_id'               => $teacherId,
                'subject_id'               => $subjectId,
                'batch_id'                 => $batchId,
                'lecture_date'             => $today,
                'period_number'            => $periodNumber,
                'start_time'               => $now,
                'status'                   => 'active',
                'teacher_gps_lat'          => $teacherLat,
                'teacher_gps_long'         => $teacherLong,
                'gps_radius_meters'        => 15,
                'attendance_window_minutes'=> 5,
            ]);
        });

        // Audit outside transaction
        $this->auditService->log('attendance_session_started', $session);

        return $session;
    }

    /**
     * Mark a student's attendance for an active session.
     * Validates: session active → window open → batch match → semester match → GPS → no duplicate.
     *
     * @param LectureSession $session
     * @param Student        $student
     * @param float          $studentLat
     * @param float          $studentLong
     * @return AttendanceRecord
     * @throws Exception
     */
    public function markAttendance(
        LectureSession $session,
        Student $student,
        float $studentLat,
        float $studentLong
    ): AttendanceRecord {

        return DB::transaction(function () use ($session, $student, $studentLat, $studentLong) {

            // 1. Session must be active
            if ($session->status !== 'active') {
                throw new Exception("Cannot mark attendance for a closed session.");
            }

            // 2. Window check
            $now         = Carbon::now();
            $windowExpiry = Carbon::parse($session->start_time)
                ->copy()
                ->addMinutes($session->attendance_window_minutes);

            if ($now->greaterThan($windowExpiry)) {
                throw new Exception("Attendance window has expired.");
            }

            // 3. Batch match — student must belong to session's batch
            if ($student->batch_id !== $session->batch_id) {
                throw new Exception("Student does not belong to the batch assigned to this lecture session.");
            }

            // 4. Semester isolation — student's current unit must match subject's semester
            $session->loadMissing('subject');
            $subjectSemester = (int) $session->subject->semester;

            if ($student->current_unit !== $subjectSemester) {
                throw new Exception(
                    "You are enrolled in Semester {$student->current_unit} and cannot mark attendance " .
                    "for a Semester {$subjectSemester} subject."
                );
            }

            // 5. GPS check
            $distance = $this->calculateDistance(
                (float) $session->teacher_gps_lat,
                (float) $session->teacher_gps_long,
                $studentLat,
                $studentLong
            );

            if ($distance > $session->gps_radius_meters) {
                throw new Exception("Student is outside the allowed GPS radius.");
            }

            // 6. Duplicate check (also enforced by DB unique constraint)
            $exists = AttendanceRecord::where('lecture_session_id', $session->id)
                ->where('student_id', $student->id)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw new Exception("Attendance has already been marked for this student in this session.");
            }

            // 7. Record
            return AttendanceRecord::create([
                'lecture_session_id' => $session->id,
                'student_id'         => $student->id,
                'status'             => 'present',
                'marked_at'          => $now,
                'student_gps_lat'    => $studentLat,
                'student_gps_long'   => $studentLong,
            ]);
        });
    }

    /**
     * Close an active session and auto-mark absent for semester-matching students who didn't mark.
     *
     * @param LectureSession $session
     * @throws Exception
     */
    public function closeSession(LectureSession $session): void
    {
        DB::transaction(function () use ($session) {

            if ($session->status === 'closed') {
                throw new Exception("Session is already closed.");
            }

            $session->status   = 'closed';
            $session->end_time = Carbon::now();
            $session->save();

            // Load batch students + subject semester info directly — no assignment table
            $session->loadMissing(['batch.students', 'subject']);
            $subjectSemester = (int) $session->subject->semester;

            // Only students in the matching semester should receive absent records
            $students = $session->batch->students->where('current_unit', $subjectSemester);

            $existingStudentIds = AttendanceRecord::where('lecture_session_id', $session->id)
                ->pluck('student_id')
                ->toArray();

            $absentRecords = [];
            $now = Carbon::now();

            foreach ($students as $student) {
                if (!in_array($student->id, $existingStudentIds)) {
                    $absentRecords[] = [
                        'lecture_session_id' => $session->id,
                        'student_id'         => $student->id,
                        'status'             => 'absent',
                        'marked_at'          => null,
                        'student_gps_lat'    => null,
                        'student_gps_long'   => null,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }
            }

            if (!empty($absentRecords)) {
                AttendanceRecord::insert($absentRecords);
            }
        });

        // Audit outside transaction
        $this->auditService->log('attendance_session_closed', $session);
    }

    /**
     * Haversine formula — distance between two GPS points in metres.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R        = 6371000; // Earth radius in metres
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
