<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LectureSession;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Stats
        $classesToday = LectureSession::where('teacher_id', $teacherId)
            ->where('lecture_date', Carbon::today())
            ->count();

        $classesThisWeek = LectureSession::where('teacher_id', $teacherId)
            ->whereBetween('lecture_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $totalClasses = LectureSession::where('teacher_id', $teacherId)->count();

        // For the Start Attendance form
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        // Check for active session
        $activeSession = LectureSession::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->where('lecture_date', Carbon::today())
            ->with(['subject', 'batch'])
            ->first();

        // If active session, get attendance count
        if ($activeSession) {
            $activeSession->present_count = \App\Models\AttendanceRecord::where('lecture_session_id', $activeSession->id)
                ->where('status', 'present')
                ->count();
            
            // For total count, we need students in that batch whose current_unit matches subject semester
            $subjectSemester = (int)$activeSession->subject->semester;
            $activeSession->total_students = \App\Models\Student::where('batch_id', $activeSession->batch_id)
                ->where('current_unit', $subjectSemester)
                ->count();
        }

        // Recent Sessions (Today & Yesterday)
        $recentSessions = LectureSession::where('teacher_id', $teacherId)
            ->whereIn('lecture_date', [Carbon::today(), Carbon::yesterday()])
            ->with(['subject.course', 'batch'])
            ->orderBy('lecture_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $todaySessions = $recentSessions->filter(fn($session) => $session->lecture_date->isToday());
        $yesterdaySessions = $recentSessions->filter(fn($session) => $session->lecture_date->isYesterday());

        return view('teacher.dashboard', compact(
            'classesToday', 'classesThisWeek', 'totalClasses',
            'courses', 'batches', 'subjects', 'activeSession',
            'todaySessions', 'yesterdaySessions'
        ));
    }
}
