<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentUnitFee;
use App\Models\InstallmentFine;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Core Counts ───────────────────────────────────────────────
        $stats = [
            'total_students'  => Student::count(),
            'active_students' => Student::where('is_active', true)->count(),
            'total_teachers'  => Teacher::count(),
            'departments'     => Department::where('is_active', true)->count(),
            'active_batches'  => Batch::where('is_active', true)->count(),
            'total_courses'   => Course::where('is_active', true)->count(),
        ];

        // ── Financial Snapshot ────────────────────────────────────────
        $financial = [
            'total_fee_billed' => StudentUnitFee::sum('unit_fee'),
            'total_collected'  => StudentUnitFee::sum('total_paid'),
            'total_pending'    => StudentUnitFee::selectRaw('SUM(unit_fee - total_paid) as diff')->value('diff') ?? 0,
            'total_fines'      => InstallmentFine::sum('fine_amount'),
            'unpaid_fines'     => InstallmentFine::where('is_paid', false)->sum('fine_amount'),
        ];

        // ── Document Stats ────────────────────────────────────────────
        $requiredDocs   = ['10th_marksheet', '12th_marksheet', 'aadhaar'];
        $docsUploaded   = DB::table('student_documents')
            ->whereIn('document_type', $requiredDocs)
            ->count();
        $docsExpected   = $stats['total_students'] * count($requiredDocs);
        $docPendingCount = Student::with('documents')
            ->get()
            ->filter(function ($s) use ($requiredDocs) {
                $uploaded = $s->documents->pluck('document_type')->toArray();
                return count(array_diff($requiredDocs, $uploaded)) > 0;
            })->count();

        // ── Batch Enrolment breakdown (top 8) ─────────────────────────
        $batchBreakdown = Batch::withCount('students')
            ->where('is_active', true)
            ->with('course:id,name')
            ->orderByDesc('students_count')
            ->limit(8)
            ->get();

        // ── Recent 5 students ─────────────────────────────────────────
        $recentStudents = Student::with([
                'user:id,name,email',
                'batch:id,name,course_id',
                'batch.course:id,name',
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'financial',
            'docPendingCount',
            'docsUploaded',
            'docsExpected',
            'batchBreakdown',
            'recentStudents',
        ));
    }
}
