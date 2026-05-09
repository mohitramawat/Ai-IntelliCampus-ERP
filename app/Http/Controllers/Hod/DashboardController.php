<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get the department linked to this HOD's user via teachers table.
     * Falls back to first active department if no teacher profile exists.
     */
    private function hodDepartment()
    {
        $teacher = Teacher::where('user_id', Auth::id())->with('department')->first();
        return $teacher?->department ?? Department::where('is_active', true)->first();
    }

    public function index()
    {
        $dept = $this->hodDepartment();

        if (!$dept) {
            return view('hod.dashboard', [
                'dept'           => null,
                'teacherCount'   => 0,
                'studentCount'   => 0,
                'batchCount'     => 0,
                'recentStudents' => collect(),
                'teachers'       => collect(),
            ]);
        }

        // Teachers in this department
        $teacherCount = Teacher::where('department_id', $dept->id)->count();

        // Batches & courses in this department
        $courseIds = Course::where('department_id', $dept->id)->pluck('id');
        $courseCount = $courseIds->count();
        $batchIds  = Batch::whereIn('course_id', $courseIds)->pluck('id');
        $batchCount = $batchIds->count();

        // Students enrolled in batches of this department's courses
        $studentCount = Student::whereIn('batch_id', $batchIds)->count();

        // Recent 5 students
        $recentStudents = Student::with(['user:id,name,email','batch:id,name,course_id','batch.course:id,name,code'])
            ->whereIn('batch_id', $batchIds)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Teachers list
        $teachers = Teacher::with('user:id,name,email')
            ->where('department_id', $dept->id)
            ->get();

        // Recent Audit Logs (Manual Overrides)
        $recentOverrides = \App\Models\AttendanceRecord::with(['student.user:id,name', 'lectureSession.subject:id,name', 'lectureSession.teacher.user:id,name'])
            ->whereIn('student_id', function ($query) use ($batchIds) {
                $query->select('id')->from('students')->whereIn('batch_id', $batchIds);
            })
            ->where('marked_by_method', 'teacher_manual')
            ->orderByDesc('marked_at')
            ->limit(5)
            ->get();

        return view('hod.dashboard', compact(
            'dept', 'teacherCount', 'studentCount', 'batchCount', 'courseCount', 'recentStudents', 'teachers', 'recentOverrides'
        ));
    }
}
