<?php

namespace App\Http\Controllers;

use App\Services\Fee\StudentFeeGenerationService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected StudentFeeGenerationService $feeGenerationService;
    protected AuditService $auditService;

    public function __construct(StudentFeeGenerationService $feeGenerationService, AuditService $auditService)
    {
        $this->feeGenerationService = $feeGenerationService;
        $this->auditService         = $auditService;
    }

    public function store(Request $request)
    {
        $student = \App\Models\Student::create($request->all());

        $this->feeGenerationService->generateForStudent($student);

        // Audit — student created
        $this->auditService->log('student_created', $student);

        return response()->json(['success' => true, 'student' => $student]);
    }
}
