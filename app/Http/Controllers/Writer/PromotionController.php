<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Student;
use App\Services\Fee\StudentFeeGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    protected StudentFeeGenerationService $feeService;

    public function __construct(StudentFeeGenerationService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        // Get batches with active students
        $batches = Batch::where('status', 'active')
            ->with('course')
            ->orderBy('name')
            ->get();

        return view('writer.promotion.index', compact('batches'));
    }

    public function getStudents(Request $request)
    {
        $batchId = $request->batch_id;
        $students = Student::where('batch_id', $batchId)
            ->where('is_active', true)
            ->where('academic_status', 'active')
            ->with('user:id,name,email')
            ->get();

        return response()->json(['students' => $students]);
    }

    public function promote(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        try {
            DB::beginTransaction();

            $promotedCount = 0;
            $students = Student::whereIn('id', $request->student_ids)->get();

            foreach ($students as $student) {
                // Increment current_unit (Semester/Year)
                $newUnit = $student->current_unit + 1;
                
                $student->update([
                    'current_unit' => $newUnit
                ]);

                // Generate Fee for the new unit
                $this->feeService->generateForStudent($student, $newUnit);

                $promotedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully promoted $promotedCount students and generated their new fees!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
