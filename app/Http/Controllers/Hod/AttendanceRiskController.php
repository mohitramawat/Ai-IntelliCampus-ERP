<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\AttendanceRiskPrediction;
use App\Services\HuggingFaceRiskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AttendanceRiskController (HOD)
 *
 * Department-wide attendance risk overview for Heads of Department.
 * Shows risk analytics, high-risk students, and allows batch predictions.
 */
class AttendanceRiskController extends Controller
{
    public function __construct(private HuggingFaceRiskService $riskService) {}

    // ─── HOD Risk Dashboard ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        // Get HOD's department via Teacher record
        $hodTeacher = Teacher::where('user_id', auth()->id())->first();
        $deptId     = $hodTeacher?->department_id;

        $riskFilter = $request->input('risk', 'all');
        $search     = $request->input('search', '');

        // All students in this department (via batch → course → department)
        $studentsQuery = Student::whereHas('batch.course', fn($q) => $q->where('department_id', $deptId))
            ->with(['user', 'batch.course', 'latestRiskPrediction']);

        if (!empty($search)) {
            $studentsQuery->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $allStudents = $studentsQuery->get();

        // Attach live attendance percentages
        $allStudents = $allStudents->map(function ($student) {
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

        // Filter by risk
        $students = $riskFilter !== 'all'
            ? $allStudents->filter(fn($s) => $s->latestRiskPrediction?->risk_level === $riskFilter)
            : $allStudents;

        // Summary stats
        $allIds      = $allStudents->pluck('id');
        $predictions = AttendanceRiskPrediction::whereIn('student_id', $allIds)->get();

        $summary = [
            'total_students' => $allStudents->count(),
            'analysed'       => $predictions->count(),
            'high'           => $predictions->where('risk_level', 'high')->count(),
            'medium'         => $predictions->where('risk_level', 'medium')->count(),
            'low'            => $predictions->where('risk_level', 'low')->count(),
            'avg_attendance' => $allStudents->avg('att_pct'),
        ];

        return view('hod.risk.index', compact('students', 'summary', 'riskFilter', 'search', 'deptId'));
    }

    // ─── Predict Department-Wide ──────────────────────────────────────────────────

    /**
     * Run AI predictions for all students in this HOD's department.
     */
    public function predictDepartment(Request $request)
    {
        $hodTeacher = Teacher::where('user_id', auth()->id())->first();
        $deptId     = $hodTeacher?->department_id;

        $students = Student::whereHas('batch.course', fn($q) => $q->where('department_id', $deptId))->get();
        $results  = ['high' => 0, 'medium' => 0, 'low' => 0, 'total' => $students->count()];

        foreach ($students as $student) {
            try {
                $pred = $this->riskService->predictRisk($student);
                $results[$pred->risk_level]++;
            } catch (\Throwable $e) {
                // continue on error
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }

    // ─── Predict Single Student (HOD) ───────────────────────────────────────────

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
}
