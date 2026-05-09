<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DepartmentViewController extends Controller
{
    private function hodDepartment()
    {
        $teacher = Teacher::where('user_id', Auth::id())->with('department')->first();
        return $teacher?->department ?? Department::where('is_active', true)->first();
    }

    // ── Teachers (read-only) ──────────────────────────────────────
    public function teachersIndex()
    {
        $dept = $this->hodDepartment();
        return view('hod.teachers', compact('dept'));
    }

    public function teachersDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $dept = $this->hodDepartment();
        if (!$dept) return DataTables::of(collect())->make(true);

        $query = Teacher::with(['user:id,name,email', 'department:id,name'])
            ->where('department_id', $dept->id)
            ->select('teachers.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name',       fn($t) => $t->user?->name ?? '—')
            ->addColumn('email',      fn($t) => $t->user?->email ?? '—')
            ->addColumn('dept_name',  fn($t) => $t->department?->name ?? '—')
            ->addColumn('status_badge', fn($t) => $t->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    // ── Students (read-only) ──────────────────────────────────────
    public function studentsIndex()
    {
        $dept    = $this->hodDepartment();
        $courses = [];
        $batches = [];
        if ($dept) {
            $courseIds = Course::where('department_id', $dept->id)->pluck('id');
            $courses   = Course::whereIn('id', $courseIds)->get(['id', 'name', 'code']);
            $batches   = Batch::whereIn('course_id', $courseIds)->get(['id', 'name', 'course_id']);
        }
        return view('hod.students', compact('dept', 'courses', 'batches'));
    }

    public function studentsDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);
        $dept = $this->hodDepartment();
        if (!$dept) return DataTables::of(collect())->make(true);

        $courseIds = Course::where('department_id', $dept->id)->pluck('id');
        $batchIds  = Batch::whereIn('course_id', $courseIds)->pluck('id');

        $query = Student::with([
            'user:id,name,email',
            'batch:id,name,course_id',
            'batch.course:id,name,code',
        ])
        ->whereIn('batch_id', $batchIds)
        ->select('students.*');

        if ($request->filled('course_id')) {
            $filteredBatches = Batch::where('course_id', $request->course_id)->pluck('id');
            $query->whereIn('batch_id', $filteredBatches);
        }
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name',         fn($s) => $s->user?->name ?? '—')
            ->addColumn('email',        fn($s) => $s->user?->email ?? '—')
            ->addColumn('batch_name',   fn($s) => ($s->batch?->course?->code ?? '') . ' — ' . ($s->batch?->name ?? '—'))
            ->addColumn('status_badge', fn($s) => $s->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->rawColumns(['status_badge'])
            ->make(true);
    }
}
