<?php

namespace App\Http\Controllers;

use App\Models\LectureSession;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * GET /student/attendance
     */
    public function index(AttendanceSummaryService $summaryService)
    {
        $student = Student::where('user_id', auth()->id())->with('batch.course')->firstOrFail();
        $summary = $summaryService->getStudentSummary($student);
        
        // Find active session for student's batch AND matching their current unit (semester)
        $activeSession = LectureSession::where('batch_id', $student->batch_id)
            ->where('status', 'active')
            ->where('lecture_date', Carbon::today())
            ->whereHas('subject', function($q) use ($student) {
                $q->where('semester', $student->current_unit);
            })
            ->with(['subject', 'teacher'])
            ->first();

        // Check if student already marked for this session
        if ($activeSession) {
            $activeSession->is_marked = AttendanceRecord::where('lecture_session_id', $activeSession->id)
                ->where('student_id', $student->id)
                ->exists();
        }

        return view('student.attendance.index', compact('student', 'summary', 'activeSession'));
    }

    /**
     * GET /teacher/attendance
     */
    public function teacherIndex()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        // Check for teacher's active session
        $activeSession = LectureSession::where('teacher_id', auth()->id())
            ->where('status', 'active')
            ->where('lecture_date', Carbon::today())
            ->with(['subject', 'batch'])
            ->first();

        // If active session, get attendance count
        if ($activeSession) {
            $activeSession->present_count = AttendanceRecord::where('lecture_session_id', $activeSession->id)
                ->where('status', 'present')
                ->count();
            
            // For total count, we need students in that batch whose current_unit matches subject semester
            $subjectSemester = (int)$activeSession->subject->semester;
            $activeSession->total_students = Student::where('batch_id', $activeSession->batch_id)
                ->where('current_unit', $subjectSemester)
                ->count();
        }

        return view('teacher.attendance.index', compact('courses', 'batches', 'subjects', 'activeSession'));
    }

    /**
     * POST /attendance/start  (role:teacher)
     *
     * Teacher selects subject + batch + period dynamically.
     * teacher_id is always taken from auth() — never from request.
     */
    public function startSession(Request $request)
    {
        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'batch_id'      => 'required|exists:batches,id',
            'period_number' => 'required|integer|min:1|max:10',
            'teacher_lat'   => 'required|numeric',
            'teacher_long'  => 'required|numeric',
        ]);

        // teacher_id is always the logged-in user — never injected from request
        $teacherId = auth()->id();

        try {
            $session = $this->attendanceService->startSession(
                $teacherId,
                $validated['subject_id'],
                $validated['batch_id'],
                $validated['period_number'],
                $validated['teacher_lat'],
                $validated['teacher_long']
            );

            return response()->json([
                'success' => true,
                'session' => $session,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /attendance/mark  (role:student)
     *
     * Student marks their own attendance for an active session.
     */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'lecture_session_id' => 'required|exists:lecture_sessions,id',
            'student_lat'        => 'required|numeric',
            'student_long'       => 'required|numeric',
        ]);

        try {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            $session = LectureSession::findOrFail($validated['lecture_session_id']);

            $record = $this->attendanceService->markAttendance(
                $session,
                $student,
                $validated['student_lat'],
                $validated['student_long']
            );

            return response()->json([
                'success' => true,
                'record'  => $record,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * POST /attendance/close  (role:teacher)
     *
     * Teacher closes their own session.
     * Ownership verified via session->teacher_id == auth()->id()
     */
    public function closeSession(Request $request)
    {
        $validated = $request->validate([
            'lecture_session_id' => 'required|exists:lecture_sessions,id',
        ]);

        $session = LectureSession::findOrFail($validated['lecture_session_id']);

        // Ownership check — teacher can only close sessions they started
        if ($session->teacher_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized session access.'], 403);
        }

        try {
            $this->attendanceService->closeSession($session);

            return response()->json([
                'success' => true,
                'message' => 'Session closed successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function summary(\App\Services\AttendanceSummaryService $summaryService)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        $summary = $summaryService->getStudentSummary($student);

        return response()->json($summary);
    }

    /**
     * GET /teacher/attendance/session/{session}/students
     */
    public function getSessionStudents(LectureSession $session)
    {
        abort_unless($session->teacher_id === auth()->id(), 403);

        $markedRecords = AttendanceRecord::where('lecture_session_id', $session->id)
            ->with('student.user')
            ->orderBy('marked_at', 'desc')
            ->get();

        $enrolledStudents = Student::where('batch_id', $session->batch_id)
            ->with('user:id,name')
            ->get();

        return response()->json([
            'marked'   => $markedRecords,
            'enrolled' => $enrolledStudents
        ]);
    }

    /**
     * GET /teacher/attendance/session/{session}/biometrics
     */
    public function getSessionBiometrics(LectureSession $session)
    {
        abort_unless($session->teacher_id === auth()->id(), 403);

        $students = Student::with('user:id,name')
            ->where('batch_id', $session->batch_id)
            ->whereNotNull('face_descriptor')
            ->get(['id', 'user_id', 'face_descriptor']);

        return response()->json($students);
    }

    /**
     * POST /teacher/attendance/session/{session}/mark-bulk
     */
    public function markBulkAttendance(Request $request, LectureSession $session)
    {
        abort_unless($session->teacher_id === auth()->id(), 403);

        $validated = $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($session->status !== 'active') {
            return response()->json(['error' => 'Cannot mark attendance for a closed session.'], 422);
        }

        $now = now();
        $records = [];
        $teacherLat = $session->teacher_gps_lat;
        $teacherLong = $session->teacher_gps_long;

        // Security Lockdown: Prevent spoofing by ensuring students actually belong to this session's batch
        $validStudentIds = \App\Models\Student::whereIn('id', $validated['student_ids'])
            ->where('batch_id', $session->batch_id)
            ->pluck('id')
            ->toArray();

        foreach ($validStudentIds as $sid) {
            $exists = AttendanceRecord::where('lecture_session_id', $session->id)
                ->where('student_id', $sid)
                ->exists();

            if (!$exists) {
                $records[] = [
                    'lecture_session_id' => $session->id,
                    'student_id'         => $sid,
                    'status'             => 'present',
                    'marked_by_method'   => 'ai',
                    'marked_at'          => $now,
                    'student_gps_lat'    => $teacherLat,
                    'student_gps_long'   => $teacherLong,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        }

        if (count($records) > 0) {
            AttendanceRecord::insert($records);
        }

        return response()->json([
            'success'      => true,
            'marked_count' => count($records)
        ]);
    }

    /**
     * POST /teacher/attendance/session/{session}/mark-manual
     * Used for Manual Override when AI misses a student
     */
    public function markManualOverride(Request $request, LectureSession $session)
    {
        abort_unless($session->teacher_id === auth()->id(), 403);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        if ($session->status !== 'active') {
            return response()->json(['error' => 'Cannot mark attendance for a closed session.'], 422);
        }

        // Validate student belongs to batch
        $studentExists = \App\Models\Student::where('id', $validated['student_id'])
            ->where('batch_id', $session->batch_id)
            ->exists();

        if (!$studentExists) {
            return response()->json(['error' => 'Student does not belong to this class.'], 422);
        }

        $alreadyMarked = AttendanceRecord::where('lecture_session_id', $session->id)
            ->where('student_id', $validated['student_id'])
            ->exists();

        if (!$alreadyMarked) {
            AttendanceRecord::create([
                'lecture_session_id' => $session->id,
                'student_id'         => $validated['student_id'],
                'status'             => 'present',
                'marked_by_method'   => 'teacher_manual',
                'marked_at'          => now(),
                'student_gps_lat'    => $session->teacher_gps_lat,
                'student_gps_long'   => $session->teacher_gps_long,
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Already marked.']);
    }
}
