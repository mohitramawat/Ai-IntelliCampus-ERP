<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Student;
use App\Models\Course;
use App\Services\StudentCreationService;
use App\Services\StudentDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    protected array $requiredDocs = [
        '10th_marksheet',
        '12th_marksheet',
        'aadhaar',
    ];

    public function __construct(
        protected StudentCreationService $creationService,
        protected StudentDocumentService $documentService
    ) {}

    /**
     * Show the student creation form.
     * Passes full department → course → batch tree as JSON for cascading JS dropdowns.
     */
    public function create()
    {
        $departments = Department::with([
            'courses' => function ($q) {
                $q->where('is_active', true)
                  ->with(['batches' => fn($q) => $q->where('status', 'active')->orderBy('name')])
                  ->orderBy('name');
            },
        ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        $departmentsJson = $departments->map(function ($dept) {
            return [
                'id'      => $dept->id,
                'name'    => $dept->name,
                'courses' => $dept->courses->map(function ($course) {
                    return [
                        'id'      => $course->id,
                        'name'    => $course->name,
                        'code'    => $course->code,
                        'batches' => $course->batches->map(fn($b) => [
                            'id'   => $b->id,
                            'name' => $b->name,
                        ])->values(),
                    ];
                })->values(),
            ];
        })->values()->toJson();

        return view('writer.students.create', compact('departmentsJson'));
    }

    /**
     * Store a new student.
     * Password is AUTO-GENERATED (no password field in form).
     * Enrollment number is auto-generated server-side.
     * Roll number is optional.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Login credentials — NO password field (auto-generated)
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users,email'],

            // Academic
            'batch_id'       => ['required', 'exists:batches,id'],
            'roll_number'    => ['nullable', 'string', 'max:50'],
            'admission_date' => ['required', 'date'],

            // Personal (all optional)
            'category'       => ['nullable', 'string', 'max:50'],
            'gender'         => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth'  => ['nullable', 'date'],
            'father_name'    => ['nullable', 'string', 'max:100'],
            'mother_name'    => ['nullable', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],

            // Documents (all optional — tracked via pending list)
            '10th_marksheet' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            '12th_marksheet' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'aadhaar'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tc'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // ── Auto-generate Enrollment Number ──────────────────────────────
        $batch      = Batch::with('course')->findOrFail($request->batch_id);
        $courseCode = strtoupper($batch->course->code);
        $year       = $batch->start_year;

        $existingCount = Student::whereHas(
            'batch',
            fn($q) => $q->where('course_id', $batch->course_id)
        )->count();

        $enrollmentNumber = sprintf('%s%d%04d', $courseCode, $year, $existingCount + 1);
        while (Student::where('enrollment_number', $enrollmentNumber)->exists()) {
            $existingCount++;
            $enrollmentNumber = sprintf('%s%d%04d', $courseCode, $year, $existingCount + 1);
        }

        // ── Auto roll number if not provided ─────────────────────────────
        $rollNumber = $request->roll_number
            ?? ($courseCode . $year . str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT));

        // ── Create student (password=null → service auto-generates it) ────
        $student = $this->creationService->create(
            [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => null,   // triggers auto-generation in StudentCreationService
            ],
            [
                'batch_id'          => $request->batch_id,
                'roll_number'       => $rollNumber,
                'enrollment_number' => $enrollmentNumber,
                'admission_date'    => $request->admission_date,
                'category'          => $request->category,
                'father_name'       => $request->father_name,
                'mother_name'       => $request->mother_name,
                'contact_number'    => $request->contact_number,
                'address'           => $request->address,
                'gender'            => $request->gender,
                'date_of_birth'     => $request->date_of_birth,
            ]
        );

        // ── Upload documents ──────────────────────────────────────────────
        foreach (['10th_marksheet', '12th_marksheet', 'aadhaar', 'tc'] as $docType) {
            if ($request->hasFile($docType) && $request->file($docType)->isValid()) {
                try {
                    $this->documentService->uploadDocument($student, $request->file($docType), $docType);
                } catch (\Exception $e) {
                    \Log::warning("Doc upload failed [{$docType}] student {$student->id}: " . $e->getMessage());
                }
            }
        }

        // ── Flash full credentials so writer can hand slip to student ─────
        return redirect()
            ->route('writer.students.create')
            ->with('student_created', [
                'name'       => $request->name,
                'email'      => $request->email,
                'enrollment' => $enrollmentNumber,
                'roll'       => $rollNumber,
                'password'   => $student->plain_password,
            ]);
    }

    /**
     * Show the student list page for writers.
     */
    public function index()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $courses     = Course::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department_id']);
        $batches     = Batch::where('is_active', true)->orderBy('name')->get(['id', 'name', 'course_id']);

        return view('writer.students.index', compact('departments', 'courses', 'batches'));
    }

    /**
     * DataTables endpoint for writers.
     * Mirror of admin but action is 'Edit'.
     */
    public function datatable(Request $request)
    {
        abort_unless($request->ajax(), 403);

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

        // Dropdown filters
        if ($request->filled('department_id')) $query->where('departments.id', $request->department_id);
        if ($request->filled('course_id'))     $query->where('courses.id',     $request->course_id);
        if ($request->filled('batch_id'))      $query->where('students.batch_id', $request->batch_id);
        if ($request->filled('status'))        $query->where('students.is_active', $request->status === 'active');

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('name',       fn($q, $k) => $q->where('users.name',   'like', "%{$k}%"))
            ->filterColumn('email',      fn($q, $k) => $q->where('users.email',  'like', "%{$k}%"))
            ->filterColumn('batch',      fn($q, $k) => $q->where('batches.name', 'like', "%{$k}%"))
            ->filterColumn('course',     fn($q, $k) => $q->where('courses.name', 'like', "%{$k}%"))
            ->filterColumn('roll_number',fn($q, $k) => $q->where('students.roll_number', 'like', "%{$k}%"))
            ->orderColumn('name',       'users.name $1')
            ->orderColumn('email',      'users.email $1')
            ->orderColumn('batch',      'batches.name $1')
            ->orderColumn('course',     'courses.name $1')
            ->orderColumn('department', 'departments.name $1')
            ->filter(function ($query) {
                $search = request()->get('search');
                $val    = data_get($search, 'value');
                if (blank($val)) return;
                $s = "%{$val}%";
                $query->where(fn($q) => $q
                    ->where('users.name',             'like', $s)
                    ->orWhere('users.email',           'like', $s)
                    ->orWhere('students.roll_number',  'like', $s)
                    ->orWhere('batches.name',          'like', $s)
                    ->orWhere('courses.name',          'like', $s)
                );
            })
            ->addColumn('doc_status', function ($s) {
                $uploaded = $s->documents->pluck('document_type')->toArray();
                $done     = count(array_intersect($this->requiredDocs, $uploaded));
                return $done . '/' . count($this->requiredDocs);
            })
            ->addColumn('status_badge', fn($s) => $s->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>'
            )
            ->addColumn('action', fn($s) =>
                '<a href="' . route('writer.students.edit', $s->id) . '" class="btn-primary text-xs py-1.5 px-3" style="display:inline-flex;align-items:center;gap:4px">
                    <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                 </a>'
            )
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Show the student edit form.
     */
    public function edit(Student $student)
    {
        $student->load(['user', 'batch.course.department']);

        // Mirror of create's dept data
        $departments = Department::with([
            'courses' => function ($q) {
                $q->where('is_active', true)
                  ->with(['batches' => fn($q) => $q->where('status', 'active')->orderBy('name')])
                  ->orderBy('name');
            },
        ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

        $departmentsJson = $departments->map(fn($dept) => [
            'id' => $dept->id,
            'name' => $dept->name,
            'courses' => $dept->courses->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'batches' => $c->batches->map(fn($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                ])->values(),
            ])->values(),
        ])->values()->toJson();

        return view('writer.students.edit', compact('student', 'departmentsJson'));
    }

    /**
     * Update student details.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'unique:users,email,' . $student->user_id],
            'batch_id'       => ['required', 'exists:batches,id'],
            'roll_number'    => ['nullable', 'string', 'max:50'],
            'admission_date' => ['required', 'date'],
            'is_active'      => ['required', 'boolean'],
            'category'       => ['nullable', 'string', 'max:50'],
            'gender'         => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth'  => ['nullable', 'date'],
            'father_name'    => ['nullable', 'string', 'max:100'],
            'mother_name'    => ['nullable', 'string', 'max:100'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $student) {
            $student->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            $student->update($request->only([
                'batch_id', 'roll_number', 'admission_date',
                'is_active', 'category', 'gender', 'date_of_birth',
                'father_name', 'mother_name', 'contact_number', 'address'
            ]));
        });

        // Handle docs if any uploaded in edit
        foreach (['10th_marksheet', '12th_marksheet', 'aadhaar', 'tc'] as $docType) {
            if ($request->hasFile($docType) && $request->file($docType)->isValid()) {
                $this->documentService->uploadDocument($student, $request->file($docType), $docType);
            }
        }

        return redirect()
            ->route('writer.students.index')
            ->with('success', 'Student record updated successfully.');
    }

    /**
     * Show students with missing required documents.
     */
    public function pendingDocuments()
    {
        $requiredDocs = $this->requiredDocs;

        $students = Student::with(['user', 'batch.course', 'documents.uploader.roles'])
            ->get()
            ->filter(function ($student) use ($requiredDocs) {
                $uploaded = $student->documents->pluck('document_type')->toArray();
                return count(array_diff($requiredDocs, $uploaded)) > 0;
            })
            ->map(function ($student) use ($requiredDocs) {
                $uploaded = $student->documents->pluck('document_type')->toArray();
                $student->missing_docs = array_values(array_diff($requiredDocs, $uploaded));
                return $student;
            });

        return view('writer.students.pending-documents', compact('students', 'requiredDocs'));
    }
}

