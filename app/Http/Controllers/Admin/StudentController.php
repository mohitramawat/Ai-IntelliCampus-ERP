<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Show All Students page with filters (departments, courses, batches).
     */
    public function index(Request $request)
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $courses = Course::where('is_active', true)
            ->with('department:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);

        $batches = Batch::where('is_active', true)
            ->with('course:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'course_id']);

        return view('admin.students.index', compact('departments', 'courses', 'batches'));
    }

    /**
     * DataTables AJAX endpoint — server-side processing.
     * JOINs related tables so every column is searchable by Yajra globally.
     */
    public function datatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        // Explicit JOINs let Yajra issue real SQL WHERE/LIKE against these
        // columns. documents is kept as eager-load since it can't be joined cleanly.
        $query = Student::with(['documents:id,student_id,document_type'])
            ->select([
                'students.*',
                'users.name       as user_name',
                'users.email      as user_email',
                'batches.name     as batch_name',
                'courses.name     as course_name',
                'departments.name as department_name',
            ])
            ->leftJoin('users',       'users.id',       '=', 'students.user_id')
            ->leftJoin('batches',     'batches.id',     '=', 'students.batch_id')
            ->leftJoin('courses',     'courses.id',     '=', 'batches.course_id')
            ->leftJoin('departments', 'departments.id', '=', 'courses.department_id');

        // --- Dropdown filters ---
        if ($request->filled('department_id')) {
            $query->where('departments.id', $request->department_id);
        }
        if ($request->filled('course_id')) {
            $query->where('courses.id', $request->course_id);
        }
        if ($request->filled('batch_id')) {
            $query->where('students.batch_id', $request->batch_id);
        }
        if ($request->filled('status')) {
            $query->where('students.is_active', $request->status === 'active');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            // Map virtual column names to their real SELECT aliases for search
            ->filterColumn('name',       fn($q, $k) => $q->where('users.name',       'like', "%{$k}%"))
            ->filterColumn('email',      fn($q, $k) => $q->where('users.email',      'like', "%{$k}%"))
            ->filterColumn('batch',      fn($q, $k) => $q->where('batches.name',     'like', "%{$k}%"))
            ->filterColumn('course',     fn($q, $k) => $q->where('courses.name',     'like', "%{$k}%"))
            ->filterColumn('department', fn($q, $k) => $q->where('departments.name', 'like', "%{$k}%"))
            ->filterColumn('roll_number',fn($q, $k) => $q->where('students.roll_number', 'like', "%{$k}%"))
            // Global search override
            ->filter(function ($query) {
                $search = request()->get('search');
                $value  = data_get($search, 'value');
                if (blank($value)) return;
                $s = "%{$value}%";
                $query->where(fn($q) => $q
                    ->where('users.name',             'like', $s)
                    ->orWhere('users.email',           'like', $s)
                    ->orWhere('students.roll_number',  'like', $s)
                    ->orWhere('students.enrollment_number', 'like', $s)
                    ->orWhere('batches.name',          'like', $s)
                    ->orWhere('courses.name',          'like', $s)
                    ->orWhere('departments.name',      'like', $s)
                );
            })
            ->addColumn('name',       fn($s) => $s->user_name ?? '—')
            ->addColumn('email',      fn($s) => $s->user_email ?? '—')
            ->addColumn('batch',      fn($s) => $s->batch_name ?? '—')
            ->addColumn('course',     fn($s) => $s->course_name ?? '—')
            ->addColumn('department', fn($s) => $s->department_name ?? '—')
            ->addColumn('doc_status', function ($s) {
                $required = ['10th_marksheet', '12th_marksheet', 'aadhaar'];
                $uploaded = $s->documents->pluck('document_type')->toArray();
                $done     = count(array_intersect($required, $uploaded));
                return $done . '/' . count($required);
            })
            ->addColumn('status_badge', fn($s) =>
                $s->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($s) =>
                '<a href="' . route('admin.students.show', $s->id) . '" class="btn-primary text-xs py-1.5 px-3" style="display:inline-flex;align-items:center;gap:4px">
                    <span class="material-symbols-outlined text-[14px]">open_in_new</span> View
                 </a>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Dedicated student detail page.
     * Full eager load — zero N+1, no raw queries.
     */
    public function show(Student $student)
    {
        $student->load([
            'user:id,name,email,created_at',
            'batch:id,name,start_year,end_year,course_id',
            'batch.course:id,name,code,unit_type,total_units,duration_years,department_id',
            'batch.course.department:id,name',
            'batch.course.feeStructures:id,course_id,total_fee,effective_from_year,is_active',
            'documents:id,student_id,document_type,file_name,file_size,mime_type,created_at',
            'unitFees:id,student_id,fee_structure_id,unit_number,unit_name,unit_fee,total_paid,status',
            'unitFees.installments:id,student_unit_fee_id,installment_number,installment_amount,paid_amount,due_date,status',
            'unitFees.installments.fines:id,student_unit_installment_id,fine_amount,applied_on,is_paid',
        ]);

        // ── Financial aggregates ─────────────────────────────────────
        $totalCourseFee  = $student->unitFees->sum('unit_fee');
        $totalPaid       = $student->unitFees->sum('total_paid');
        $totalPending    = $totalCourseFee - $totalPaid;
        $allFines        = $student->unitFees
            ->flatMap(fn($uf) => $uf->installments)
            ->flatMap(fn($i)  => $i->fines);
        $totalFines      = $allFines->sum('fine_amount');
        $unpaidFines     = $allFines->where('is_paid', false)->sum('fine_amount');
        $collectionPct   = $totalCourseFee > 0
            ? round(($totalPaid / $totalCourseFee) * 100, 1) : 0;

        // ── Document completeness ────────────────────────────────────
        $requiredDocs  = ['10th_marksheet', '12th_marksheet', 'aadhaar'];
        $uploadedTypes = $student->documents->pluck('document_type')->toArray();
        $missingDocs   = array_values(array_diff($requiredDocs, $uploadedTypes));

        return view('admin.students.show', compact(
            'student',
            'totalCourseFee', 'totalPaid', 'totalPending',
            'totalFines', 'unpaidFines', 'collectionPct',
            'requiredDocs', 'missingDocs',
        ));
    }
}
