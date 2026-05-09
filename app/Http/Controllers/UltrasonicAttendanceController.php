<?php

namespace App\Http\Controllers;

use App\Models\LectureSession;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UltrasonicAttendanceController extends Controller
{
    /**
     * GET /teacher/ultrasonic-attendance
     * Futuristic UI for the teacher.
     */
    public function teacherIndex()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $activeSession = LectureSession::where('teacher_id', auth()->id())
            ->where('status', 'active')
            ->where('is_ultrasonic', true)
            ->where('lecture_date', Carbon::today())
            ->with(['subject', 'batch'])
            ->first();

        if ($activeSession) {
            $activeSession->present_count = AttendanceRecord::where('lecture_session_id', $activeSession->id)
                ->where('status', 'present')
                ->count();
            
            $subjectSemester = (int)$activeSession->subject->semester;
            $activeSession->total_students = Student::where('batch_id', $activeSession->batch_id)
                ->where('current_unit', $subjectSemester)
                ->count();
        }

        return view('teacher.ultrasonic.index', compact('courses', 'batches', 'subjects', 'activeSession'));
    }

    /**
     * POST /teacher/ultrasonic-attendance/start
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

        $teacherId = auth()->id();

        // Generate a random 4-digit token
        $token = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        try {
            $session = LectureSession::create([
                'teacher_id'                => $teacherId,
                'subject_id'                => $validated['subject_id'],
                'batch_id'                  => $validated['batch_id'],
                'period_number'             => $validated['period_number'],
                'lecture_date'              => Carbon::today(),
                'start_time'                => now(),
                'status'                    => 'active',
                'teacher_gps_lat'           => $validated['teacher_lat'],
                'teacher_gps_long'          => $validated['teacher_long'],
                'gps_radius_meters'         => 50,
                'attendance_window_minutes' => 15,
                'is_ultrasonic'             => true,
                'ultrasonic_token'          => $token,
            ]);

            return response()->json([
                'success' => true,
                'session' => $session,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start ultrasonic session: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /student/ultrasonic-attendance
     * Futuristic UI for the student.
     */
    public function studentIndex()
    {
        $student = Student::where('user_id', auth()->id())->with('batch.course')->firstOrFail();
        
        $activeSession = LectureSession::where('batch_id', $student->batch_id)
            ->where('status', 'active')
            ->where('is_ultrasonic', true)
            ->where('lecture_date', Carbon::today())
            ->with(['subject', 'teacher'])
            ->first();

        if ($activeSession) {
            $activeSession->is_marked = AttendanceRecord::where('lecture_session_id', $activeSession->id)
                ->where('student_id', $student->id)
                ->exists();
        }

        return view('student.ultrasonic.index', compact('student', 'activeSession'));
    }

    /**
     * POST /student/ultrasonic-attendance/mark
     */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'lecture_session_id' => 'required|exists:lecture_sessions,id',
            'student_lat'        => 'required|numeric',
            'student_long'       => 'required|numeric',
            'token'              => 'required|string',
        ]);

        try {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            $session = LectureSession::findOrFail($validated['lecture_session_id']);

            if (!$session->is_ultrasonic) {
                throw new Exception('This is not an ultrasonic session.');
            }

            $realToken = $session->ultrasonic_token;
            $submittedToken = $validated['token'];

            // Since the audio loops continuously, the student might catch the token out of order (e.g. 3412 instead of 1234).
            // We validate by checking if the submitted 4 digits are a cyclic permutation of the real token.
            $isValid = false;
            if (strlen($realToken) === 4 && strlen($submittedToken) === 4) {
                $doubled = $realToken . $realToken;
                if (strpos($doubled, $submittedToken) !== false) {
                    $isValid = true;
                }
            }

            if (!$isValid && $realToken !== $submittedToken) {
                throw new Exception('Invalid Audio Token! Are you outside the class?');
            }

            // Check if already marked
            $exists = AttendanceRecord::where('lecture_session_id', $session->id)
                ->where('student_id', $student->id)
                ->exists();

            if ($exists) {
                throw new Exception('Attendance already marked.');
            }

            $record = AttendanceRecord::create([
                'lecture_session_id' => $session->id,
                'student_id'         => $student->id,
                'status'             => 'present',
                'marked_at'          => now(),
                'student_lat'        => $validated['student_lat'],
                'student_long'       => $validated['student_long'],
                'device_info'        => request()->userAgent(),
                'ip_address'         => request()->ip(),
            ]);

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
}
