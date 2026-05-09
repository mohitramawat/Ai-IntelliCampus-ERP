<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use App\Models\FeeStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterDataController extends Controller
{
    // ── Main overview page ────────────────────────────────────────
    public function index()
    {
        $stats = [
            'departments' => Department::count(),
            'courses'     => Course::count(),
            'batches'     => Batch::count(),
            'subjects'    => Subject::count(),
            'fees'        => FeeStructure::count(),
        ];

        $departments = Department::with(['courses' => fn($q) => $q->withCount(['batches', 'subjects'])])
            ->withCount('courses')
            ->get();

        return view('admin.master.index', compact('stats', 'departments'));
    }

    // ── Departments DataTable ─────────────────────────────────────
    public function departmentsDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $query = Department::with('campus:id,name')->withCount('courses')->select('departments.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('courses_count', fn($d) => $d->courses_count)
            ->addColumn('campus_name',   fn($d) => $d->campus?->name ?? '—')
            ->addColumn('status_badge', fn($d) => $d->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($d) =>
                '<div class="flex gap-1 justify-center">
                    <a href="' . route('admin.master.courses', ['department_id' => $d->id]) . '" class="btn-view" title="View Courses">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                    </a>
                    <button onclick="confirmDeleteDept(' . $d->id . ',\'' . addslashes($d->name) . '\')" class="btn-del" title="Delete">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    // ── Courses DataTable ─────────────────────────────────────────
    public function coursesDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $query = Course::with('department:id,name')->withCount(['batches','subjects'])->select('courses.*');
        if ($request->filled('department_id')) $query->where('department_id', $request->department_id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('batches_count', fn($c) => $c->batches_count)
            ->addColumn('subjects_count', fn($c) => $c->subjects_count)
            ->addColumn('dept_name',    fn($c) => $c->department?->name ?? '—')
            ->addColumn('unit_info',    fn($c) => ucfirst($c->unit_type) . ' · ' . $c->total_units)
            ->addColumn('status_badge', fn($c) => $c->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($c) =>
                '<div class="flex gap-1 justify-center">
                    <a href="' . route('admin.master.batches', ['course_id' => $c->id]) . '" class="btn-view" title="View Batches">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                    </a>
                    <button onclick="confirmDelete(\'course\',' . $c->id . ',\'' . addslashes($c->name) . '\')" class="btn-del" title="Delete">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    // ── Batches DataTable ─────────────────────────────────────────
    public function batchesDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $query = Batch::with('course:id,name,code')->withCount('students')->select('batches.*');
        if ($request->filled('course_id')) $query->where('course_id', $request->course_id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('students_count', fn($b) => $b->students_count)
            ->addColumn('course_name',  fn($b) => ($b->course?->code ?? '') . ' — ' . ($b->course?->name ?? '—'))
            ->addColumn('year_range',   fn($b) => $b->start_year . ' – ' . $b->end_year)
            ->addColumn('status_badge', fn($b) => match($b->status) {
                'active'    => '<span class="badge badge-success">Active</span>',
                'completed' => '<span class="badge badge-info">Completed</span>',
                default     => '<span class="badge badge-danger">Inactive</span>',
            })
            ->addColumn('action', fn($b) =>
                '<div class="flex gap-1 justify-center">
                    <button onclick="confirmDelete(\'batch\',' . $b->id . ',\'' . addslashes($b->name) . '\')" class="btn-del" title="Delete">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    // ── Subjects DataTable ────────────────────────────────────────
    public function subjectsDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $query = Subject::with('course:id,name,code')->select('subjects.*');
        if ($request->filled('course_id')) $query->where('course_id', $request->course_id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('course_name',  fn($s) => ($s->course?->code ?? '') . ' — ' . ($s->course?->name ?? '—'))
            ->addColumn('sem_label',    fn($s) => 'Sem ' . $s->semester)
            ->addColumn('status_badge', fn($s) => $s->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($s) =>
                '<div class="flex gap-1 justify-center">
                    <button onclick="confirmDelete(\'subject\',' . $s->id . ',\'' . addslashes($s->name) . '\')" class="btn-del" title="Delete">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    // ── Named route helpers for filtered views ────────────────────
    public function coursesView(Request $request)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get(['id','name']);
        $selectedDept = $request->department_id;
        return view('admin.master.courses', compact('departments', 'selectedDept'));
    }

    public function batchesView(Request $request)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get(['id','name','code']);
        $selectedCourse = $request->course_id;
        return view('admin.master.batches', compact('courses', 'selectedCourse'));
    }

    // ── Delete actions ────────────────────────────────────────────
    public function destroyDepartment(Department $department)
    {
        if ($department->courses()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Department has courses.'], 422);
        }
        $department->delete();
        return response()->json(['success' => true, 'message' => 'Department deleted.']);
    }

    public function destroyCourse(Course $course)
    {
        if ($course->batches()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Course has batches.'], 422);
        }
        $course->delete();
        return response()->json(['success' => true, 'message' => 'Course deleted.']);
    }

    public function destroyBatch(Batch $batch)
    {
        if ($batch->students()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Batch has students.'], 422);
        }
        $batch->delete();
        return response()->json(['success' => true, 'message' => 'Batch deleted.']);
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return response()->json(['success' => true, 'message' => 'Subject deleted.']);
    }
}
