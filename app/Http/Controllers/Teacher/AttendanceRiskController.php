<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AttendanceRiskPrediction;
use App\Models\Teacher;
use App\Services\HuggingFaceRiskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AttendanceRiskController (Teacher)
 *
 * Allows teachers to view and trigger AI-based attendance risk
 * predictions for students in their assigned batches/subjects.
 */
class AttendanceRiskController extends Controller
{
    public function __construct(private HuggingFaceRiskService $riskService) {}

    // ─── Main Dashboard ──────────────────────────────────────────────────────────

    /**
     * Show the AI Attendance Risk dashboard for teachers.
     */
    public function index(Request $request)
    {
        // Get students relevant to this teacher's sessions
        $teacherId  = auth()->id();
        $riskFilter = $request->input('risk', 'all');
        $search     = $request->input('search', '');

        // Get all students who have at least one attendance record
        // in a session conducted by this teacher
        $studentIds = DB::table('lecture_sessions')
            ->where('teacher_id', $teacherId)
            ->join('attendance_records', 'lecture_sessions.id', '=', 'attendance_records.lecture_session_id')
            ->distinct()
            ->pluck('attendance_records.student_id');

        $studentsQuery = Student::whereIn('id', $studentIds)
            ->with(['user', 'batch.course', 'latestRiskPrediction']);

        // Search filter
        if (!empty($search)) {
            $studentsQuery->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $students = $studentsQuery->get();

        // Compute attendance percentages and attach
        $students = $students->map(function ($student) {
            $records = DB::table('attendance_records')
                ->where('student_id', $student->id)
                ->selectRaw('COUNT(*) as total, SUM(status = "present") as present')
                ->first();

            $student->att_total   = $records->total ?? 0;
            $student->att_present = $records->present ?? 0;
            $student->att_pct     = $student->att_total > 0
                ? round(($student->att_present / $student->att_total) * 100, 1)
                : 0;

            return $student;
        });

        // Apply risk filter
        if ($riskFilter !== 'all') {
            $students = $students->filter(function ($s) use ($riskFilter) {
                return $s->latestRiskPrediction?->risk_level === $riskFilter;
            });
        }

        // Summary counts
        $predictions = AttendanceRiskPrediction::whereIn('student_id', $studentIds)->get();
        $summary = [
            'total'  => $students->count(),
            'high'   => $predictions->where('risk_level', 'high')->count(),
            'medium' => $predictions->where('risk_level', 'medium')->count(),
            'low'    => $predictions->where('risk_level', 'low')->count(),
        ];

        return view('teacher.risk.index', compact('students', 'summary', 'riskFilter', 'search'));
    }

    // ─── Predict Single Student ──────────────────────────────────────────────────

    /**
     * Trigger AI prediction for a single student. Returns JSON for AJAX.
     */
    public function predict(Request $request, Student $student)
    {
        try {
            $prediction = $this->riskService->predictRisk($student);

            return response()->json([
                'success'          => true,
                'risk_level'       => $prediction->risk_level,
                'risk_label'       => $prediction->risk_label,
                'ai_remark'        => $prediction->ai_remark,
                'suggested_action' => $prediction->suggested_action,
                'att_pct'          => $prediction->attendance_percentage,
                'badge_class'      => $prediction->risk_badge_class,
                'color_class'      => $prediction->risk_color_class,
                'updated_at'       => $prediction->updated_at->diffForHumans(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─── Predict All (Batch) ─────────────────────────────────────────────────────

    /**
     * Run AI prediction for all students associated with this teacher.
     * Returns JSON summary for AJAX.
     */
    public function predictAll(Request $request)
    {
        $teacherId = auth()->id();

        $studentIds = DB::table('lecture_sessions')
            ->where('teacher_id', $teacherId)
            ->join('attendance_records', 'lecture_sessions.id', '=', 'attendance_records.lecture_session_id')
            ->distinct()
            ->pluck('attendance_records.student_id');

        $students = Student::whereIn('id', $studentIds)->get();
        $results  = ['high' => 0, 'medium' => 0, 'low' => 0, 'total' => $students->count()];

        foreach ($students as $student) {
            try {
                $pred = $this->riskService->predictRisk($student);
                $results[$pred->risk_level]++;
            } catch (\Throwable $e) {
                Log::warning("Risk prediction failed for student #{$student->id}: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }
}
