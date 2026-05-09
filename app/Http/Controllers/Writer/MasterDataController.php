<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Subject;
use App\Models\FeeStructure;
use App\Models\CourseUnitFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterDataController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // DEPARTMENTS
    // ═══════════════════════════════════════════════════════════

    public function departmentsIndex()
    {
        $campus = Campus::first();
        return view('writer.master.departments.index', compact('campus'));
    }

    public function departmentsDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        $query = Department::with('campus:id,name')
            ->select('departments.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('campus_name', fn($d) => $d->campus?->name ?? '—')
            ->addColumn('status_badge', fn($d) => $d->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($d) =>
                '<div class="flex gap-2">
                    <button onclick="editDept('.$d->id.')" class="btn-icon-edit" title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <button onclick="deleteDept('.$d->id.',\''.$d->name.'\')" class="btn-icon-del" title="Delete">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function departmentsStore(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', 'unique:departments,code'],
            'is_active' => ['boolean'],
        ]);

        $campus = Campus::first();
        if (!$campus) {
            return response()->json(['success' => false, 'message' => 'No Campus found in database. Please seed the Campus table first.'], 404);
        }

        $dept = Department::create([
            'campus_id' => $campus->id,
            'name'      => $data['name'],
            'code'      => strtoupper($data['code']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'message' => 'Department created.', 'data' => $dept]);
    }

    public function departmentsShow(Department $department)
    {
        return response()->json($department);
    }

    public function departmentsUpdate(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', 'unique:departments,code,' . $department->id],
            'is_active' => ['boolean'],
        ]);

        $department->update([
            'name'      => $data['name'],
            'code'      => strtoupper($data['code']),
            'is_active' => $data['is_active'] ?? $department->is_active,
        ]);

        return response()->json(['success' => true, 'message' => 'Department updated.']);
    }

    public function departmentsDestroy(Department $department)
    {
        if ($department->courses()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Department has courses linked to it.'], 422);
        }
        $department->delete();
        return response()->json(['success' => true, 'message' => 'Department deleted.']);
    }

    // ═══════════════════════════════════════════════════════════
    // COURSES
    // ═══════════════════════════════════════════════════════════

    public function coursesIndex()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('writer.master.courses.index', compact('departments'));
    }

    public function coursesDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        $query = Course::with('department:id,name')
            ->select('courses.*');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('department_name', fn($c) => $c->department?->name ?? '—')
            ->addColumn('unit_label', fn($c) =>
                ucfirst($c->unit_type) . ' · ' . $c->total_units . ' ' . ($c->unit_type === 'semester' ? 'Semesters' : 'Years')
            )
            ->addColumn('status_badge', fn($c) => $c->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($c) =>
                '<div class="flex gap-2">
                    <button onclick="editCourse('.$c->id.')" class="btn-icon-edit" title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <button onclick="deleteCourse('.$c->id.',\''.$c->name.'\')" class="btn-icon-del" title="Delete">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function coursesStore(Request $request)
    {
        $data = $request->validate([
            'department_id'  => ['required', 'exists:departments,id'],
            'name'           => ['required', 'string', 'max:150'],
            'code'           => ['required', 'string', 'max:20', 'unique:courses,code'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:6'],
            'unit_type'      => ['required', 'in:semester,year'],
            'total_units'    => ['required', 'integer', 'min:1', 'max:12'],
            'description'    => ['nullable', 'string'],
            'is_active'      => ['boolean'],
        ]);

        $course = Course::create([
            ...$data,
            'code'      => strtoupper($data['code']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'message' => 'Course created.', 'data' => $course]);
    }

    public function coursesShow(Course $course)
    {
        $course->load('department:id,name');
        return response()->json($course);
    }

    public function coursesUpdate(Request $request, Course $course)
    {
        $data = $request->validate([
            'department_id'  => ['required', 'exists:departments,id'],
            'name'           => ['required', 'string', 'max:150'],
            'code'           => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
            'duration_years' => ['required', 'integer', 'min:1', 'max:6'],
            'unit_type'      => ['required', 'in:semester,year'],
            'total_units'    => ['required', 'integer', 'min:1', 'max:12'],
            'description'    => ['nullable', 'string'],
            'is_active'      => ['boolean'],
        ]);

        $course->update([...$data, 'code' => strtoupper($data['code'])]);

        return response()->json(['success' => true, 'message' => 'Course updated.']);
    }

    public function coursesDestroy(Course $course)
    {
        if ($course->batches()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Course has batches linked to it.'], 422);
        }
        $course->delete();
        return response()->json(['success' => true, 'message' => 'Course deleted.']);
    }

    // ═══════════════════════════════════════════════════════════
    // BATCHES
    // ═══════════════════════════════════════════════════════════

    public function batchesIndex()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        return view('writer.master.batches.index', compact('courses'));
    }

    public function batchesDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        $query = Batch::with('course:id,name,code')
            ->select('batches.*');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('course_name', fn($b) => ($b->course?->code ?? '') . ' — ' . ($b->course?->name ?? '—'))
            ->addColumn('year_range', fn($b) => $b->start_year . ' – ' . $b->end_year)
            ->addColumn('status_badge', fn($b) => match($b->status) {
                'active'    => '<span class="badge badge-success">Active</span>',
                'completed' => '<span class="badge badge-info">Completed</span>',
                default     => '<span class="badge badge-danger">Inactive</span>',
            })
            ->addColumn('action', fn($b) =>
                '<div class="flex gap-2">
                    <button onclick="editBatch('.$b->id.')" class="btn-icon-edit" title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <button onclick="deleteBatch('.$b->id.',\''.$b->name.'\')" class="btn-icon-del" title="Delete">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function batchesStore(Request $request)
    {
        $data = $request->validate([
            'course_id'  => ['required', 'exists:courses,id'],
            'name'       => ['required', 'string', 'max:100'],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'end_year'   => ['required', 'integer', 'min:2000', 'max:2099', 'gte:start_year'],
            'status'     => ['required', 'in:active,completed,inactive'],
            'is_active'  => ['boolean'],
        ]);

        $batch = Batch::create([...$data, 'is_active' => $data['is_active'] ?? true]);

        return response()->json(['success' => true, 'message' => 'Batch created.', 'data' => $batch]);
    }

    public function batchesShow(Batch $batch)
    {
        $batch->load('course:id,name,code');
        return response()->json($batch);
    }

    public function batchesUpdate(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'course_id'  => ['required', 'exists:courses,id'],
            'name'       => ['required', 'string', 'max:100'],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'end_year'   => ['required', 'integer', 'min:2000', 'max:2099', 'gte:start_year'],
            'status'     => ['required', 'in:active,completed,inactive'],
            'is_active'  => ['boolean'],
        ]);

        $batch->update($data);
        return response()->json(['success' => true, 'message' => 'Batch updated.']);
    }

    public function batchesDestroy(Batch $batch)
    {
        if ($batch->students()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete: Batch has students enrolled.'], 422);
        }
        $batch->delete();
        return response()->json(['success' => true, 'message' => 'Batch deleted.']);
    }

    // ═══════════════════════════════════════════════════════════
    // SUBJECTS
    // ═══════════════════════════════════════════════════════════

    public function subjectsIndex()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'total_units', 'unit_type']);
        return view('writer.master.subjects.index', compact('courses'));
    }

    public function subjectsDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        $query = Subject::with('course:id,name,code')
            ->select('subjects.*');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('course_name', fn($s) => ($s->course?->code ?? '') . ' — ' . ($s->course?->name ?? '—'))
            ->addColumn('sem_label',   fn($s) => 'Sem ' . $s->semester)
            ->addColumn('status_badge', fn($s) => $s->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($s) =>
                '<div class="flex gap-2">
                    <button onclick="editSubject('.$s->id.')" class="btn-icon-edit" title="Edit">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </button>
                    <button onclick="deleteSubject('.$s->id.',\''.$s->name.'\')" class="btn-icon-del" title="Delete">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function subjectsStore(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'semester'  => ['required', 'integer', 'min:1', 'max:12'],
            'is_active' => ['boolean'],
        ]);

        $subject = Subject::create([...$data, 'is_active' => $data['is_active'] ?? true]);

        return response()->json(['success' => true, 'message' => 'Subject created.', 'data' => $subject]);
    }

    public function subjectsShow(Subject $subject)
    {
        $subject->load('course:id,name,code,total_units,unit_type');
        return response()->json($subject);
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name'      => ['required', 'string', 'max:150'],
            'code'      => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
            'semester'  => ['required', 'integer', 'min:1', 'max:12'],
            'is_active' => ['boolean'],
        ]);

        $subject->update($data);
        return response()->json(['success' => true, 'message' => 'Subject updated.']);
    }

    public function subjectsDestroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['success' => true, 'message' => 'Subject deleted.']);
    }

    // ═══════════════════════════════════════════════════════════
    // FEE STRUCTURES
    // ═══════════════════════════════════════════════════════════

    public function feesIndex()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'total_units', 'unit_type']);
        return view('writer.master.fees.index', compact('courses'));
    }

    public function feesDatatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

        $query = FeeStructure::with(['course:id,name,code', 'unitFees'])
            ->select('fee_structures.*');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('course_name', fn($f) => ($f->course?->code ?? '') . ' — ' . ($f->course?->name ?? '—'))
            ->addColumn('unit_count',  fn($f) => $f->unitFees->count() . ' units')
            ->addColumn('total_fee_fmt', fn($f) => '₹' . number_format($f->total_fee, 0))
            ->addColumn('status_badge', fn($f) => $f->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($f) =>
                '<div class="flex gap-2">
                    <a href="' . route('writer.master.fees.show', $f->id) . '" class="btn-icon-edit" title="View / Edit" style="text-decoration:none">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </a>
                    <button onclick="deleteFee(' . $f->id . ',\'' . addslashes($f->course?->name ?? '') . ' ' . $f->effective_from_year . '\')" class="btn-icon-del" title="Delete">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store fee structure + all unit fees in one transaction.
     * Body: { course_id, effective_from_year, total_fee, currency, is_active, units: [{unit_number, unit_name, unit_fee}] }
     */
    public function feesStore(Request $request)
    {
        $data = $request->validate([
            'course_id'           => ['required', 'exists:courses,id'],
            'effective_from_year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'total_fee'           => ['required', 'numeric', 'min:0'],
            'currency'            => ['nullable', 'string', 'max:5'],
            'is_active'           => ['boolean'],
            'units'               => ['required', 'array', 'min:1'],
            'units.*.unit_number' => ['required', 'integer', 'min:1'],
            'units.*.unit_name'   => ['required', 'string', 'max:50'],
            'units.*.unit_fee'    => ['required', 'numeric', 'min:0'],
        ]);

        // Prevent duplicate (course + year)
        if (FeeStructure::where('course_id', $data['course_id'])->where('effective_from_year', $data['effective_from_year'])->exists()) {
            return response()->json(['success' => false, 'message' => 'A fee structure for this course and year already exists.'], 422);
        }

        $fee = DB::transaction(function () use ($data) {
            $fs = FeeStructure::create([
                'course_id'           => $data['course_id'],
                'effective_from_year' => $data['effective_from_year'],
                'total_fee'           => $data['total_fee'],
                'currency'            => $data['currency'] ?? 'INR',
                'is_active'           => $data['is_active'] ?? true,
            ]);

            foreach ($data['units'] as $unit) {
                CourseUnitFee::create([
                    'fee_structure_id' => $fs->id,
                    'unit_number'      => $unit['unit_number'],
                    'unit_name'        => $unit['unit_name'],
                    'unit_fee'         => $unit['unit_fee'],
                    'is_active'        => true,
                ]);
            }

            return $fs;
        });

        return response()->json(['success' => true, 'message' => 'Fee structure created.', 'id' => $fee->id]);
    }

    /**
     * Show the fee structure detail/edit page.
     */
    public function feesShow(FeeStructure $feeStructure)
    {
        $feeStructure->load(['course', 'unitFees' => fn($q) => $q->orderBy('unit_number')]);
        $courses = Course::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'total_units', 'unit_type']);
        return view('writer.master.fees.show', compact('feeStructure', 'courses'));
    }

    /**
     * Update header info only (course, year, total, currency, active).
     */
    public function feesUpdate(Request $request, FeeStructure $feeStructure)
    {
        $data = $request->validate([
            'effective_from_year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'total_fee'           => ['required', 'numeric', 'min:0'],
            'currency'            => ['nullable', 'string', 'max:5'],
            'is_active'           => ['boolean'],
            'units'               => ['required', 'array', 'min:1'],
            'units.*.id'          => ['nullable', 'exists:course_unit_fees,id'],
            'units.*.unit_number' => ['required', 'integer', 'min:1'],
            'units.*.unit_name'   => ['required', 'string', 'max:50'],
            'units.*.unit_fee'    => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $feeStructure) {
            $feeStructure->update([
                'effective_from_year' => $data['effective_from_year'],
                'total_fee'           => $data['total_fee'],
                'currency'            => $data['currency'] ?? 'INR',
                'is_active'           => $data['is_active'] ?? true,
            ]);

            // Sync unit fees: delete all, re-insert
            $feeStructure->unitFees()->delete();
            foreach ($data['units'] as $unit) {
                CourseUnitFee::create([
                    'fee_structure_id' => $feeStructure->id,
                    'unit_number'      => $unit['unit_number'],
                    'unit_name'        => $unit['unit_name'],
                    'unit_fee'         => $unit['unit_fee'],
                    'is_active'        => true,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Fee structure updated.']);
    }

    public function feesDestroy(FeeStructure $feeStructure)
    {
        DB::transaction(function () use ($feeStructure) {
            $feeStructure->unitFees()->delete();
            $feeStructure->delete();
        });
        return response()->json(['success' => true, 'message' => 'Fee structure deleted.']);
    }
}

