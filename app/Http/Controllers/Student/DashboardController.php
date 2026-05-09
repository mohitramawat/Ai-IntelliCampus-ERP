<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', Auth::id())->with('batch.course')->firstOrFail();
        
        // Stats
        $attendanceTotal = AttendanceRecord::where('student_id', $student->id)->count();
        $attendancePresent = AttendanceRecord::where('student_id', $student->id)->where('status', 'present')->count();
        $attendancePercentage = $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 100, 1) : 0;
        
        $documentCount = StudentDocument::where('student_id', $student->id)->count();

        return view('student.dashboard', compact('student', 'attendancePercentage', 'documentCount'));
    }
}
